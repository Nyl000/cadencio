import Vue from 'vue'
import Vuex from 'vuex'

import login from 'js/Stores/Modules/Login';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        login,
    },
    strict: false,
    plugins: []
})