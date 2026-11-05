import { createStore } from 'vuex';

const store = createStore({
  state: {
    authenticated: false,
    authInitialized: false,
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
    setAuthInitialized(state, payload) {
      state.authInitialized = payload;
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
    getUser({ commit, state }, { force = false } = {}) {
      if (state.authInitialized && !force) {
        return Promise.resolve(state.authenticated);
      }

      return axios.get('/api/user')
        .then(res => {
          const authenticated = Boolean(res.data);
          commit('setUser', res.data || {});
          commit('setAuthenticated', authenticated);
          return authenticated;
        })
        .catch(() => {
          commit('setUser', {});
          commit('setAuthenticated', false);
          return false;
        })
        .finally(() => {
          commit('setAuthInitialized', true);
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
