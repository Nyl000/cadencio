import Vue from 'vue'
import Vuex from 'vuex'
import {getHooks} from 'js/Services/HookHandler';

import login from 'js/Stores/Modules/Login';

Vue.use(Vuex);

let modules  = { login }
let modulesHooks = getHooks('register_vuex_module');

modulesHooks.forEach((mod) => {
    modules = {...modules, ...mod};
});

console.log(modules);


export default new Vuex.Store({
    modules: modules,
    strict: false,
    plugins: []
})