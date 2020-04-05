import {addHook} from 'js/Services/HookHandler';
import {hasPermission} from 'js/Models/User';

import PlanningView from 'tpl/PlanningView.vue';
import PlanningStatusIndex from 'tpl/PlanningStatusIndex.vue';
import PlanningIndex from 'tpl/PlanningIndex.vue';

addHook('register_route', () => { return {path: '/planning/view/:id/:page?', component: PlanningView}});
addHook('register_route', () => { return {path: '/task_status/:page?', component: PlanningStatusIndex}});
addHook('register_route', () => { return {path: '/planning/:page?', component: PlanningIndex}});

addHook('register_menu_section', () => {
            return {
                name: 'cadencio_plannings',
                title : 'Plannings',
                canDisplay: hasPermission('planning','read') || hasPermission('planning_status','read')}
            });

addHook('register_menu', () => {
            return {
                section:'cadencio_plannings',
                title : 'Plannings',
                to : '/planning',
                canDisplay: hasPermission('planning','read')}
            });

addHook('register_menu', () => {
            return {
                section:'cadencio_plannings',
                title : 'Task Status',
                to : '/task_status',
                canDisplay: hasPermission('planning_status','read')}
            });