import {addEvent} from 'js/Services/EventHandler';
import {hasPermission} from 'js/Models/User';

import PlanningView from 'tpl/PlanningView.vue';
import PlanningStatusIndex from 'tpl/PlanningStatusIndex.vue';
import PlanningIndex from 'tpl/PlanningIndex.vue';

addEvent('register_route', () => { return {path: '/planning/view/:id/:page?', component: PlanningView}});
addEvent('register_route', () => { return {path: '/planning_status/:page?', component: PlanningStatusIndex}});
addEvent('register_route', () => { return {path: '/planning/:page?', component: PlanningIndex}});


addEvent('register_menu', () => {return {iconClass :  'fad fa-calendar-alt' , title : 'Plannings', to : '/planning', canDisplay: hasPermission('planning','read')}});
addEvent('register_menu', () => {return {iconClass :  'fad fa-tags' , title : 'Planning Status', to : '/planning_status', canDisplay: hasPermission('planning_status','read')}});