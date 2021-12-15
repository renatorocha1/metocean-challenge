import Vue from "vue"
import Vuex from "vuex"

import axios from "axios"

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    data: null,
    labels: null,
    viewMode: "all",
    loading: true,
    error: null,
  },
  mutations: {
    SET_DATA(state, data) {
      state.data = data
    },
    SET_LABELS(state, data) {
      state.labels = data
    },
    SET_LOADING(state, status) {
      state.loading = status
    },
    SET_VIEW_MODE(state, type) {
      state.viewMode = type
    },
    SET_ERROR(state, error) {
      state.error = error
    },
  },
  actions: {
    setLabels(context, data) {
      context.commit("SET_LABELS", data)
    },
    setViewMode(context, type) {
      context.commit("SET_VIEW_MODE", type)
    },
    loadData(context, date) {
      context.commit("SET_LOADING", true)
      axios
        .get(`/api/data?date=${date}`)
        .then((result) => {
          context.commit("SET_DATA", result.data)
        })
        .catch((error) => {
          context.commit("SET_ERROR", error)
        })
        .finally(() => context.commit("SET_LOADING", false))
    },
  },
  getters: {},
})

export default store
