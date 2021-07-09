import Vue from 'vue'
import VueRouter from 'vue-router'
import i18n from 'js/Translations/_translations';

import {testToken} from 'js/Models/User';

import App from 'tpl/App.vue'
import Login from 'tpl/Login.vue'
import Home from 'tpl/Home.vue'
import UsersIndex from 'tpl/UsersIndex.vue';
import LogsIndex from 'tpl/LogsIndex.vue';
import RolesIndex from 'tpl/RolesIndex.vue';
import ProfileIndex from 'tpl/ProfileIndex.vue';
import ModulesIndex from 'tpl/ModulesIndex.vue';
import SettingsIndex from 'tpl/SettingsIndex.vue';
import AskPasswordReset from 'tpl/AskPasswordReset.vue';
import PasswordReset from 'tpl/PasswordReset.vue';

import {getHooks} from 'js/Services/HookHandler';
import {refreshActivesModules} from 'js/Models/Module';
import Vuex from "vuex";
import store from 'js/Stores/StoreIndex';

var instance = false;


class Main {

	constructor() {

		if (!instance) {

			document.title = Config.appName;
			var routes = [
				{path: '/login', component: Login},
				{path: '/users/:page?', component: UsersIndex},
				{path: '/logs/:page?', component: LogsIndex},
				{path: '/userprofile', component: ProfileIndex},
				{path: '/roles/:page?', component: RolesIndex},
				{path: '/modules/:page?', component: ModulesIndex},
				{path: '/settings', component: SettingsIndex},
				{path: '/passwordreset', component: AskPasswordReset},
				{path: '/confirmreset/:hash', component: PasswordReset},
				{path: '/:page?', component: Home},

			];

			var routeHooks = getHooks('register_route');

			routeHooks.forEach((route) => {
				routes.push(route());
			});
			this.router = new VueRouter({
				mode: 'history',
				base: __dirname,
				routes: routes
			});


			let token = localStorage.getItem('token');
			let router = this.router;
			if (!token) {
				if (!this.isResetPasswordRoute()) {
					router.push('login');
				}

			}
			else {
				testToken(token).then(
					(datas) => {
						localStorage.setItem('user', JSON.stringify(datas.user))
						refreshActivesModules();
					},
					() => {
						if (!this.isResetPasswordRoute()) {
							router.push('login');
						}
					}
				);
			}
			let cssHooks = getHooks('css_overrides');

			//load CSS overrides
			cssHooks.forEach((cssHook) => {
				cssHook()
			});

			new Vue({
				router,
				i18n,
				store,
				render: h => h(App)
			}).$mount('#app');

			instance = this;
		}
	}

	tokenInvalidFallback() {
		if (!this.isResetPasswordRoute()) {
			instance.router.push('login');
		}
	}

	isResetPasswordRoute() {
		return /confirmreset\//.test(window.location.href)
	}

}

export {
	Main,
	instance
};



