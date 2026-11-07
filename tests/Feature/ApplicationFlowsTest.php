<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ApplicationFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_protected_api_routes(): void
    {
        $this->getJson('/api/search-user?name=Alice')
            ->assertUnauthorized();
    }

    public function test_user_can_register_and_login_with_a_stateful_spa_request(): void
    {
        Notification::fake();
        $registration = [
            'name' => 'Alice Example',
            'username' => 'alice',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->postJson('/api/register', $registration)
            ->assertCreated()
            ->assertJsonPath('message', 'Registration successful. Check your email for a verification link to finish setting up your account.');

        $user = User::where('username', 'alice')->firstOrFail();
        Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $notification) use ($user) {
            $url = $notification->toMail($user)->actionUrl;

            return str_contains($url, '/email/verify/'.$user->id.'/'.sha1($user->email))
                && URL::hasValidSignature(Request::create($url));
        });
        $this->assertNull($user->email_verified_at);
        $this->assertTrue(Hash::check('password123', $user->password));

        $this->withHeaders([
            'Origin' => 'http://localhost',
            'Referer' => 'http://localhost',
        ])->postJson('/api/login', [
            'username' => 'alice',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('message', 'Login successful!');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_uses_the_same_error_for_unknown_users_and_wrong_passwords(): void
    {
        $user = User::factory()->create([
            'username' => 'alice',
            'password' => Hash::make('password123'),
        ]);

        $unknownUserResponse = $this->postJson('/api/login', [
            'username' => 'unknown',
            'password' => 'password123',
        ]);
        $wrongPasswordResponse = $this->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'wrongpassword',
        ]);

        $unknownUserResponse->assertUnauthorized();
        $wrongPasswordResponse->assertUnauthorized();
        $this->assertSame(
            $unknownUserResponse->json('message'),
            $wrongPasswordResponse->json('message')
        );
    }

    public function test_login_is_throttled_per_username_and_ip(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/login', [
                'username' => 'missing',
                'password' => 'wrongpassword',
            ])->assertUnauthorized();
        }

        $this->postJson('/api/login', [
            'username' => 'missing',
            'password' => 'wrongpassword',
        ])->assertTooManyRequests();
    }

    public function test_unverified_users_must_verify_before_using_application_apis(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->getJson('/api/search-user?name=Alice')
            ->assertForbidden();

        $this->postJson('/api/email/verification-notification')
            ->assertAccepted()
            ->assertJsonPath('message', 'A new verification link has been sent to your email address.');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_signed_email_verification_link_marks_the_account_as_verified(): void
    {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(30),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $this->get($url)->assertRedirect('/email-verified');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_updating_email_resets_verification_and_sends_a_new_link(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'alice@example.com']);

        $this->actingAs($user)
            ->postJson('/api/settings/details', [
                'name' => $user->name,
                'username' => $user->username,
                'email' => 'alice.new@example.com',
            ])
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'alice.new@example.com',
            'email_verified_at' => null,
        ]);
        Notification::assertSentTo($user->fresh(), VerifyEmail::class);
    }

    public function test_missing_profile_returns_not_found(): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson('/api/profiles/not-a-user')
            ->assertNotFound();
    }

    public function test_password_reset_flow_sends_a_link_and_revokes_existing_tokens(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'alice@example.com',
            'password' => Hash::make('oldpassword1'),
        ]);
        $user->createToken('existing-device');
        $token = null;

        $this->postJson('/api/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('message', 'If an account exists for that email, a password reset link has been sent.');

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ])->assertOk()
            ->assertJsonPath('message', 'Your password has been reset. You can now sign in.');

        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->withHeaders([
            'Origin' => 'http://localhost',
            'Referer' => 'http://localhost',
        ])->postJson('/api/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Logout successful!');

        $this->assertGuest('web');
    }

    public function test_guest_cannot_logout(): void
    {
        $this->postJson('/api/logout')->assertUnauthorized();
    }

    public function test_authenticated_user_can_retrieve_their_email_verification_state(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('email_verified_at', null)
            ->assertJsonMissingPath('password');
    }

    public function test_authenticated_user_can_search_users_and_view_a_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Alice Example',
            'username' => 'alice',
        ]);
        User::factory()->create([
            'name' => 'Bob Example',
            'username' => 'bob',
        ]);

        $this->actingAs($user)
            ->getJson('/api/search-user?name=Bob')
            ->assertOk()
            ->assertJsonFragment(['username' => 'bob']);

        $this->getJson('/api/profiles/bob')
            ->assertOk()
            ->assertJsonFragment(['username' => 'bob']);
    }

    public function test_feed_and_search_reject_unbounded_pagination_inputs(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/posts?limit=999')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit']);

        $this->getJson('/api/posts?offset=-1')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['offset']);

        $this->getJson('/api/search-user?name=Alice&limit=999')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['limit']);

        $this->getJson('/api/search-user?name='.str_repeat('a', 101))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_authenticated_user_can_create_and_like_a_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user, 'author')->create([
            'content' => 'An existing post',
        ]);

        $this->actingAs($user)
            ->postJson('/api/post', ['content' => 'A new post'])
            ->assertCreated()
            ->assertJsonPath('message', 'Create post successful!');

        $this->assertDatabaseHas('posts', [
            'author_id' => $user->id,
            'content' => 'A new post',
        ]);

        $this->postJson("/api/posts/{$post->id}/like")
            ->assertOk()
            ->assertJsonFragment(['is_liked' => true]);

        $this->assertDatabaseHas('post_likes', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_authenticated_user_can_update_and_delete_their_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user, 'author')->create([
            'content' => 'Original post',
        ]);

        $this->actingAs($user)
            ->putJson("/api/posts/{$post->id}", ['content' => 'Updated post'])
            ->assertOk();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => 'Updated post',
        ]);

        $this->deleteJson("/api/posts/{$post->id}")
            ->assertOk();

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_cannot_update_or_delete_another_users_post(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->for($owner, 'author')->create([
            'content' => 'Owner post',
        ]);
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->putJson("/api/posts/{$post->id}", ['content' => 'Unauthorized edit'])
            ->assertNotFound();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => 'Owner post',
        ]);

        $this->deleteJson("/api/posts/{$post->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => 'Owner post',
        ]);
    }

    public function test_repeated_like_requests_do_not_duplicate_a_like(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user, 'author')->create();

        $this->actingAs($user)->postJson("/api/posts/{$post->id}/like")->assertOk();
        $this->postJson("/api/posts/{$post->id}/like")->assertOk();

        $this->assertDatabaseCount('post_likes', 1);
    }

    public function test_unique_like_migration_deduplicates_legacy_rows(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user, 'author')->create();
        $migration = require base_path('database/migrations/2026_11_06_000000_add_unique_user_post_to_post_likes_table.php');

        $migration->down();
        DB::table('post_likes')->insert([
            ['user_id' => $user->id, 'post_id' => $post->id],
            ['user_id' => $user->id, 'post_id' => $post->id],
        ]);

        $migration->up();

        $this->assertDatabaseCount('post_likes', 1);
    }

    public function test_authenticated_user_can_update_profile_and_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Alice Example',
            'username' => 'alice',
            'password' => Hash::make('oldpassword1'),
        ]);
        $user->createToken('existing-device');

        $this->actingAs($user)
            ->postJson('/api/settings/details', [
                'name' => 'Alice Updated',
                'username' => 'alice_updated',
                'email' => $user->email,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Update details successful!');

        $this->postJson('/api/settings/password', [
            'current_password' => 'oldpassword1',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ])->assertOk()
            ->assertJsonPath('message', 'Update password successful!');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'alice_updated',
            'email' => $user->email,
        ]);
        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_password_change_requires_the_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword1'),
        ]);

        $this->actingAs($user)
            ->postJson('/api/settings/password', [
                'password' => 'newpassword1',
                'password_confirmation' => 'newpassword1',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_authenticated_user_can_upload_an_avatar(): void
    {
        config(['filesystems.default' => 'public']);
        Storage::fake('public');
        $user = User::factory()->create();
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/r9sAAAAASUVORK5CYII=',
            true
        );
        $this->assertNotFalse($png);

        $this->actingAs($user)
            ->postJson('/api/settings/upload-avatar', [
                'avatar' => UploadedFile::fake()->createWithContent('avatar.png', $png),
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Upload avatar successful!');

        $this->assertNotNull($user->fresh()->image_path);
        Storage::disk('public')->assertExists($user->fresh()->image_path);
        $this->assertSame(
            Storage::disk('public')->url($user->fresh()->image_path),
            $user->fresh()->public_image_path
        );
    }
}
