import "vue-swatches/dist/vue-swatches.min.css"
import 'vue-simple-markdown/dist/vue-simple-markdown.css';
import 'vue-material-design-icons/styles.css';
import 'vue-datetime/dist/vue-datetime.min.css';
import 'vue-material/dist/vue-material.min.css';
import 'vue-select/dist/vue-select.css';

import 'vue-material/dist/theme/default.css';
import 'css/app.scss'
import VueSimpleMarkdown from 'vue-simple-markdown';
import Vue from 'vue'
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import VueMaterial from 'vue-material'
import vSelect from 'vue-select'


import {Main} from 'js/App';

Vue.use(VueRouter);
Vue.use(VueSimpleMarkdown);
Vue.use(Vuex);
Vue.use(VueMaterial);
Vue.component('v-select', vSelect)

new Main();

