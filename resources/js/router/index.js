import { createRouter, createWebHistory } from 'vue-router';
import store from '../store';

import LoginForm from '../pages/LoginForm.vue';
import RegistrationForm from '../pages/RegistrationForm.vue';
import ForgotPasswordForm from '../pages/ForgotPasswordForm.vue';
import ResetPasswordForm from '../pages/ResetPasswordForm.vue';
import VerifyEmailPage from '../pages/VerifyEmailPage.vue';
import EmailVerifiedPage from '../pages/EmailVerifiedPage.vue';
import HomePage from '../pages/HomePage.vue';
import ProfilePage from '../pages/ProfilePage.vue';
import SettingsPage from '../pages/SettingsPage.vue';
import SearchUserPage from '../pages/SearchUserPage.vue';
import PageNotFound from '../pages/PageNotFound.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Microblog';

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginForm,
      meta: {
        middleware: 'guest',
        title: 'Login',
      }
    },
    {
      path: '/register',
      name: 'register',
      component: RegistrationForm,
      meta: {
        middleware: 'guest',
        title: 'Register',
      }
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: ForgotPasswordForm,
      meta: {
        middleware: 'guest',
        title: 'Forgot Password',
      }
    },
    {
      path: '/reset-password/:token',
      name: 'reset-password',
      component: ResetPasswordForm,
      meta: {
        middleware: 'guest',
        title: 'Reset Password',
      }
    },
    {
      path: '/verify-email',
      name: 'verify-email',
      component: VerifyEmailPage,
      meta: {
        title: 'Verify Email',
      }
    },
    {
      path: '/email-verified',
      name: 'email-verified',
      component: EmailVerifiedPage,
      meta: {
        public: true,
        title: 'Email Verified',
      }
    },
    {
      path: '/home',
      name: 'home',
      component: HomePage,
      meta: {
        title: 'Home',
      }
    },
    {
      path: '/profile/:username?',
      name: 'profile',
      component: ProfilePage,
      meta: {
        title: 'Profile',
      }
    },
    {
      path: '/settings',
      name: 'settings',
      component: SettingsPage,
      meta: {
        title: 'Settings',
      }
    },
    {
      path: '/search-user/:username',
      name: 'search-user',
      component: SearchUserPage,
      meta: {
        title: 'Search User',
      }
    },
    {
      path: '/404',
      name: '404',
      component: PageNotFound,
      meta: {
        title: 'Page Not Found',
      }
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/404',
    }
  ],
});

router.beforeEach(async (to) => {
  document.title = `${to.meta.title} - ${appName}`;

  if (to.meta.public) {
    return true;
  }

  if (!store.state.authInitialized) {
    await store.dispatch('getUser');
  }

  if (to.meta.middleware === 'guest') {
    if (store.state.authenticated) {
      return store.state.user.email_verified_at ? { name: 'home' } : { name: 'verify-email' };
    }

    return true;
  }

  if (store.state.authenticated) {
    if (!store.state.user.email_verified_at && to.name !== 'verify-email') {
      return { name: 'verify-email' };
    }
    if (store.state.user.email_verified_at && to.name === 'verify-email') {
      return { name: 'home' };
    }

    return true;
  }

  return { name: 'login' };
});

export default router;
