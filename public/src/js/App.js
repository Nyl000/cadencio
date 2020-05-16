import Vue from 'vue'
import VueRouter from 'vue-router'

import {testToken} from 'js/Models/User';

import App from 'tpl/App.vue'
import Login from 'tpl/Login.vue'
import Home from 'tpl/Home.vue'
import UsersIndex from 'tpl/UsersIndex.vue';
import RolesIndex from 'tpl/RolesIndex.vue';
import ProfileIndex from 'tpl/ProfileIndex.vue';
import ModulesIndex from 'tpl/ModulesIndex.vue';
import SettingsIndex from 'tpl/SettingsIndex.vue';
import {getHooks} from 'js/Services/HookHandler';
import {refreshActivesModules} from 'js/Models/Module';

var instance = false;

class Main {

	constructor() {

		if (!instance) {

			document.title = Config.appName;
			var routes = [
				{path: '/', component: Home},
				{path: '/login', component: Login},
				{path: '/users/:page?', component: UsersIndex},
				{path: '/userprofile', component: ProfileIndex},
				{path: '/roles/:page?', component: RolesIndex},
				{path: '/modules/:page?', component: ModulesIndex},
				{path: '/settings', component: SettingsIndex},

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
				router.push('login');
			}
			else {
				testToken(token).then(
					(datas) => {
						localStorage.setItem('user', JSON.stringify(datas.user))
						refreshActivesModules();
					},
					() => {
						router.push('login');
					}
				);
			}

			new Vue({
				router,
				render: h => h(App)
			}).$mount('#app');

			instance = this;
		}
	}

	tokenInvalidFallback() {
		instance.router.push('login');
	}
}

export {
	Main,
	instance
};



