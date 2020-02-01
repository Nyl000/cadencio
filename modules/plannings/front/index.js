import {addEvent} from 'js/Services/EventHandler';
import {hasPermission} from 'js/Models/User';

import PlanningView from 'tpl/PlanningView.vue';
import PlanningStatusIndex from 'tpl/PlanningStatusIndex.vue';
import PlanningIndex from 'tpl/PlanningIndex.vue';

addEvent('register_route', () => { return {path: '/planning/view/:id/:page?', component: PlanningView}});
addEvent('register_route', () => { return {path: '/task_status/:page?', component: PlanningStatusIndex}});
addEvent('register_route', () => { return {path: '/planning/:page?', component: PlanningIndex}});

addEvent('register_menu_section', () => {return {name: 'plannings', title : 'Plannings', canDisplay: hasPermission('planning','read')}});

addEvent('register_menu', () => {return {section:'plannings', title : 'Plannings', to : '/planning', canDisplay: hasPermission('planning','read')}});
addEvent('register_menu', () => {return {section:'plannings', title : 'Task Status', to : '/task_status', canDisplay: hasPermission('planning_status','read')}});