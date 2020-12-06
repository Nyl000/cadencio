
import fr from 'js/Translations/fr';
import en from 'js/Translations/en';

import Vue from 'vue'
import VueI18n from 'vue-i18n';


const user = localStorage.getItem('user')  ?  JSON.parse(localStorage.getItem('user')) : { options : { lang : 'fr'}};

Vue.use(VueI18n);


export default new VueI18n({
    locale : typeof user.options.lang !== 'undefined' ? user.options.lang : Config.defaultLang || 'en',
    fallbackLocale: 'en',
    messages : {
        fr : fr,
        en : en
    }
})