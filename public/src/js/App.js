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
import store from 'js/Stores/StoreIndex';
import * as Sentry from "@sentry/vue";
import {Integrations} from "@sentry/tracing";

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


            if (typeof Config.sentry_dns !== 'undefined' && typeof Config.sentry_origins !== 'undefined') {
                Sentry.init({
                    Vue,
                    dsn: Config.sentry_dns,
                    integrations: [
                        new Integrations.BrowserTracing({
                            routingInstrumentation: Sentry.vueRouterInstrumentation(this.router),
                            tracingOrigins: Config.sentry_origins,
                        }),
                    ],
                    // Set tracesSampleRate to 1.0 to capture 100%
                    // of transactions for performance monitoring.
                    // We recommend adjusting this value in production
                    tracesSampleRate: 1.0,
                });
            }


            let token = localStorage.getItem('token');
            let router = this.router;
            if (!token) {
                if (!this.isResetPasswordRoute()) {
                    router.push('login');
                }

            } else {
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


            //translate VueMaterial
			Vue.material.locale.dateFormat = i18n.t('dateFormat');
			Vue.material.locale.firstDayOfAWeek = i18n.t('firstDayOfAWeek');
			Vue.material.locale.confirm = i18n.t('Ok');
			Vue.material.locale.cancel = i18n.t('Cancel');

			Vue.material.locale.days = [
                i18n.t('Sunday'),
                i18n.t('Monday'),
                i18n.t('Tuesday'),
                i18n.t('Wednesday'),
                i18n.t('Thursday'),
                i18n.t('Friday'),
                i18n.t('Saturday')
            ];
            Vue.material.locale.shortDays = [
                i18n.t('Short_Sunday'),
                i18n.t('Short_Monday'),
                i18n.t('Short_Tuesday'),
                i18n.t('Short_Wednesday'),
                i18n.t('Short_Thursday'),
                i18n.t('Short_Friday'),
                i18n.t('Short_Saturday')
            ];
            Vue.material.locale.shorterDays = [
                i18n.t('Letter_Sunday'),
                i18n.t('Letter_Monday'),
                i18n.t('Letter_Tuesday'),
                i18n.t('Letter_Wednesday'),
                i18n.t('Letter_Thursday'),
                i18n.t('Letter_Friday'),
                i18n.t('Letter_Saturday')
            ];
            Vue.material.locale.months = [
                i18n.t('January'),
                i18n.t('February'),
                i18n.t('March'),
                i18n.t('April'),
                i18n.t('May'),
                i18n.t('June'),
                i18n.t('July'),
                i18n.t('August'),
                i18n.t('September'),
                i18n.t('October'),
                i18n.t('November'),
                i18n.t('December')
            ];

            Vue.material.locale.shortMonths = [
				i18n.t('Short_January'),
				i18n.t('Short_February'),
				i18n.t('Short_March'),
				i18n.t('Short_April'),
				i18n.t('Short_May'),
				i18n.t('Short_June'),
				i18n.t('Short_July'),
				i18n.t('Short_August'),
				i18n.t('Short_September'),
				i18n.t('Short_October'),
				i18n.t('Short_November'),
				i18n.t('Short_December')
			];

			Vue.material.locale.shorterMonths = [
				i18n.t('Letter_January'),
				i18n.t('Letter_February'),
				i18n.t('Letter_March'),
				i18n.t('Letter_April'),
				i18n.t('Letter_May'),
				i18n.t('Letter_June'),
				i18n.t('Letter_July'),
				i18n.t('Letter_August'),
				i18n.t('Letter_September'),
				i18n.t('Letter_October'),
				i18n.t('Letter_November'),
				i18n.t('Letter_December')
			];

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



