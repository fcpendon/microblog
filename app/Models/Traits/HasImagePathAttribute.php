<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * @property string|null $image_path
 */
trait HasImagePathAttribute
{
    public function getPublicImagePathAttribute(): string | null
    {
        if (!$this->image_path) {
            return null;
        } elseif (config('filesystems.default') === 's3') {
            return Storage::temporaryUrl(
                $this->image_path,
                now()->addMinutes(config('filesystems.disks.s3.presigned_url_mins'))
            );
        }

        return Storage::url($this->image_path);
    }
}
