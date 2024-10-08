import { createStore } from 'vuex';

export default createStore({
  state: {
    userRole: null, // Изначально роль пользователя равна null
    userId: null,
  },
  mutations: {
    setUserRole(state, role) {
      state.userRole = role; // Установка роли пользователя
    },
    resetUserRole(state) {
      state.userRole = null; // Сброс роли пользователя
    },

    setUserId(state, id) {
      state.userId = id; // Установка роли пользователя
    },

    resetUserId(state) {
      state.userId = null; // Сброс роли пользователя
    },
  },
  actions: {
    initializeUserRole({ commit }, role) {
      commit('setUserRole', role); // Инициализация роли пользователя
    },
    logoutUser({ commit }) {
      commit('resetUserRole'); // Сброс роли пользователя при выходе
    },
  },
  getters: {
    userRole(state) {
      return state.userRole; // Геттер для получения роли пользователя
    },
    isAdmin(state) {
      return state.userRole === 'admin'; // Проверка, является ли пользователь администратором
    },

    userId(state) {
      return state.userId; // Геттер для получения роли пользователя
    },
  },
});
