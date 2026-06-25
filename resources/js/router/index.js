import Vue from 'vue';
import Router from 'vue-router';
import store from '../store';
import RegistrationForm from '../pages/RegistrationForm';
import LoginForm from '../pages/LoginForm';
import HomePage from '../pages/HomePage';
import SearchUserPage from '../pages/SearchUserPage';
import SettingsPage from '../pages/SettingsPage';
import ProfilePage from '../pages/ProfilePage';
import PageNotFound from '../pages/PageNotFound';

Vue.use(Router);

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
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
      path: '*',
      redirect: '404',
    }
  ],
});

router.beforeEach((to, from, next) => {
  document.title = `${to.meta.title} - ${process.env.MIX_APP_NAME}`
  if (to.meta.middleware === 'guest') {
    if (store.state.authenticated) {
      next({ name: 'home' });
    } else {
      next();
    }
  } else {
    if (store.state.authenticated) {
      next();
    } else {
      next({ name: 'login' });
    }
  }
});

export default router;
