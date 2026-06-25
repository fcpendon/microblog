import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    authenticated: false,
    user: {},
    alert: {
      show: false,
      message: '',
      type: '',
    },
  },
  mutations: {
    setAuthenticated(state, payload) {
      state.authenticated = payload;
    },
    setUser(state, payload) {
      state.user = payload;
    },
    setAlert(state, payload) {
      state.alert.message = payload.message;
      state.alert.type = payload.type;
      state.alert.show = true;
    },
    hideAlert(state) {
      state.alert.show = false;
    }
  },
  actions: {
    getUser({ commit }) {
      return axios.get('/api/user')
        .then(res => {
          if (res.data) {
            commit('setUser', res.data);
            commit('setAuthenticated', true);
          } else {
            commit('setUser', {});
            commit('setAuthenticated', false);
          }
        })
        .catch(err => {
          console.log(err);
        });
    },
    setAlertSuccess({ commit }, payload) {
      commit('setAlert', {
        message: payload,
        type: 'primary',
      });
    },
    setAlertError({ commit }, payload) {
      if (typeof payload !== 'string') {
        if (payload.errors) {
          let errors = Object.values(payload.errors).flat();
          payload = errors[0];
        } else {
          payload = payload.message;
        }
      }
      commit('setAlert', {
        message: payload,
        type: 'error',
      })
    },
    hideAlert({ commit }) {
      commit('hideAlert');
    },
  },
});

export default store;
