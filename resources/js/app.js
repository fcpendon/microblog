require('./bootstrap');

// Require Vue
import Vue from 'vue';
import vuetify from './vuetify';
import router from './router';
import store from './store';
import App from './App.vue';

Vue.config.productionTip = false;
Vue.config.devtools = false;

store.dispatch('getUser').then(() => {
  new Vue({
    el: '#app',
    vuetify,
    router,
    store,
    render: h => h(App)
  });
});
