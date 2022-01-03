
import fr from 'js/Translations/fr';
import en from 'js/Translations/en';

import {getHooks} from 'js/Services/HookHandler';

import Vue from 'vue'
import VueI18n from 'vue-i18n';


const user = localStorage.getItem('user')  ?  JSON.parse(localStorage.getItem('user')) : { options : { lang : 'fr'}};

Vue.use(VueI18n);

var langsHooks = getHooks('register_lang_strings');
const messages = {};
langsHooks.forEach((langItem) => {
    langItem = langItem();
    messages[langItem.langcode] = typeof messages[langItem.langcode] !== 'undefined' ? {...messages[langItem.langcode],  ...langItem.messages} :  langItem.messages;

});

messages.fr = typeof messages.fr !== 'undefined' ? {...messages.fr, ...fr} : fr;
messages.en = typeof messages.en !== 'undefined' ? {...messages.en, ...en} : en;

export default new VueI18n({
    locale : typeof user.options.lang !== 'undefined' ? user.options.lang : Config.defaultLang || 'en',
    fallbackLocale: 'en',
    messages : messages
})