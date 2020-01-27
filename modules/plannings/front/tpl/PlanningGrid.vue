<template>
    <div>
        <table class="list">
            <transition>
                <div class="loader" v-if="!loaded">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </transition>
            <thead>
            <tr class="items title">
                <th class="information selector">
                    <input type="checkbox" v-on:click="massEditAll($event)"/>
                </th>
                <th class="information nowrap" v-on:click="setOrder('id')">
                    N°
                    <menu-down-icon v-if="planning_entries_order == 'id' && planning_entries_orderDirection == 'ASC'" />
                    <menu-up-icon v-if="planning_entries_order == 'id' && planning_entries_orderDirection == 'DESC'" />
                </th>
                <th class="information nowrap" v-on:click="setOrder('id_assigned_to')">
                    Assigned To
                    <menu-down-icon v-if="planning_entries_order == 'id_assigned_to' && planning_entries_orderDirection == 'ASC'" />
                    <menu-up-icon v-if="planning_entries_order == 'id_assigned_to' && planning_entries_orderDirection == 'DESC'" />
                </th>
                <th v-if="!id_planning" class="information nowrap" v-on:click="setOrder('planning_title')">
                    Planning
                    <menu-down-icon v-if="planning_entries_order == 'planning_title' && planning_entries_orderDirection == 'ASC'" />
                    <menu-up-icon v-if="planning_entries_order == 'planning_title' && planning_entries_orderDirection == 'DESC'" />
                </th>
                <th class="information nowrap" v-on:click="setOrder('id_status')">
                    Status
                    <menu-down-icon v-if="planning_entries_order == 'id_status' && planning_entries_orderDirection == 'ASC'" />
                    <menu-up-icon v-if="planning_entries_order == 'id_status' && planning_entries_orderDirection == 'DESC'" />
                </th>
                <th class="information nowrap" v-on:click="setOrder('title')">
                    Title
                    <menu-down-icon v-if="planning_entries_order == 'title' && planning_entries_orderDirection == 'ASC'" />
                    <menu-up-icon v-if="planning_entries_order == 'title' && planning_entries_orderDirection == 'DESC'" />
                </th>
                <th class="information nowrap" v-on:click="setOrder('date_start')">
                    Start Date
                    <menu-down-icon v-if="planning_entries_order == 'date_start' && planning_entries_orderDirection == 'ASC'" />
                    <menu-up-icon v-if="planning_entries_order == 'date_start' && planning_entries_orderDirection == 'DESC'" />
                </th>
                <th class="information nowrap" v-on:click="setOrder('date_end')">
                    Due Date
                    <menu-down-icon v-if="planning_entries_order == 'date_end' && planning_entries_orderDirection == 'ASC'" />
                    <menu-up-icon v-if="planning_entries_order == 'date_end' && planning_entries_orderDirection == 'DESC'" />
                </th>
                <th class="information nowrap" v-on:click="setOrder('date_end')">
                    Deadline
                    <menu-down-icon v-if="planning_entries_order == 'date_end' && planning_entries_orderDirection == 'ASC'" />
                    <menu-up-icon v-if="planning_entries_order == 'date_end' && planning_entries_orderDirection == 'DESC'" />
                </th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr class="items" v-for="entry in planning_entries" :key="entry.id">
                <td>
                    <input
                            type="checkbox"
                            v-bind:checked="typeof massEditIds[entry.id] !== 'undefined'"
                            v-on:click.prevent="toogleMassEdit(entry.id)"
                    />
                </td>
                <td>#{{entry.id}}</td>
                <td class="titleBlock information">
                    <EditableList
                            v-bind:canupdate="hasPermission('planning_entry','update')"
                            v-bind:list="users"
                            v-bind:value="entry.id_assigned_to"
                            v-bind:saveurl="'/planning_entry/'+entry.id"
                            field="id_assigned_to"
                            placeholder="Select a user"
                    />
                </td>
                <td v-if="!id_planning" class="nowrap">
                    <router-link :to="'/planning/view/'+entry.id_planning">{{entry.planning_title}}</router-link>
                </td>
                <td class="titleBlock information nowrap">
                    <div class="status_color_wrap" :style="{backgroundColor: entry.color}">
                        <EditableList
                                v-bind:canupdate="hasPermission('planning_entry','update')"
                                v-bind:list="statuses"
                                v-bind:value="entry.id_status"
                                v-bind:saveurl="'/planning_entry/'+entry.id"
                                field="id_status"
                                :callback-success="refresh"
                                placeholder="Choisissez un status"
                        />
                    </div>
                </td>
                <td class="titleBlock information">
                    <EditableText
                            v-bind:canupdate="hasPermission('planning_entry','update')"
                            v-bind:value="entry.title"
                            v-bind:saveurl="'/planning_entry/'+entry.id"
                            field="title"
                            placeholder="Title"
                    />
                </td>
                <td class="titleBlock information nowrap">
                    <EditableDateTime
                            v-bind:canupdate="hasPermission('planning_entry','update')"
                            v-bind:value="entry.date_start"
                            v-bind:saveurl="'/planning_entry/'+entry.id"
                            field="date_start"
                            placeholder="Start Date"
                    />
                </td>
                <td class="titleBlock information nowrap">
                    <EditableDateTime
                            v-bind:canupdate="hasPermission('planning_entry','update')"
                            v-bind:value="entry.date_end"
                            v-bind:saveurl="'/planning_entry/'+entry.id"
                            field="date_end"
                            placeholder="Due Date"
                    />
                </td>
                <td class="titleBlock information nowrap">
                    <TimeRemain v-bind:value="entry.date_end"/>
                </td>
                <td class="titleBlock information nowrap">
                    <dots-horizontal-icon
                            v-if="hasPermission('planning_entry','update')"
                            class="icongrid"
                            title="Detail"
                            v-on:click="descriptionPlanningEntryModal(entry)"
                    />
                    <bell-off-icon
                            v-if="canFollow(entry) && entry.followed == 0"
                            class="icongrid"
                            title="Follow"
                            v-on:click="toggleFollow(entry.id)"
                    />
                    <bell-icon
                            v-if="canFollow(entry) && entry.followed == 1"
                            class="icongrid blue"
                            title="Unfollow"
                            v-on:click="toggleFollow(entry.id)"
                    />
                    <delete-icon v-if="hasPermission('planning_entry','delete')"
                                 class="icongrid delete"
                                 title="Delete entry"
                                 v-on:click="deleteEntryItem(entry.id)" />
                </td>
            </tr>
            </tbody>
        </table>
        <paginator :paginator="planning_entries_paginator" :path="paginator_path"/>
        <div class="quickaction">
            <select class="quickActionSelect" v-model="quickActionSelector">
                <option value>Quick action</option>
                <option value="change_status">Change status</option>
            </select>
            <div class="quickactionitem" v-if="quickActionSelector === 'change_status'">
                <select class="quickActionSelect" v-model="massEditStatusValue">
                    <option value key>Choose status</option>
                    <option
                            v-for="(status,id) in statusesWithColor"
                            v-on:click="toggleStatusFilter(id)"
                            v-bind:key="id"
                            :value="id"
                    >{{status.title}}
                    </option>
                </select>
                <button v-on:click.prevent="massEditStatus">OK</button>
            </div>
        </div>
        <Modale ref="viewEntryDescriptionModal" class="modale-markdown">
            <h1 class="title">#{{selectedEntry.id}} - {{selectedEntry.title}}</h1>
            <hr/>
            <PlanningEntryDescription :entry="selectedEntry"/>
        </Modale>
    </div>
</template>


<script>

    import {hasPermission, selectList as listUsers, getLoggedUser} from 'js/Models/User';
    import {list, listByPlanning, update, massUpdate, add, deleteItem, toggleFollow} from 'js/Models/PlanningEntry';
    import Paginator from 'tpl/Ui/Paginator.vue';
    import {selectList as listStatuses, selectWithColor as listStatusesWithColor}  from 'js/Models/PlanningStatus';
    import PlanningEntryDescription from 'tpl/PlanningEntryDescription.vue';
    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableDateTime from 'tpl/Ui/EditableDateTime.vue';
    import EditableList from 'tpl/Ui/EditableList.vue';
    import TimeRemain from 'tpl/Ui/TimeRemain.vue';
    import Modale from 'tpl/Ui/Modale.vue';
    import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue';
    import MenuUpIcon from 'vue-material-design-icons/MenuUp.vue';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';
    import BellIcon from 'vue-material-design-icons/Bell.vue';
    import BellOffIcon from 'vue-material-design-icons/BellOff.vue';
    import DotsHorizontalIcon from 'vue-material-design-icons/DotsHorizontal.vue';

    export default {
        props: ['id_planning', 'paginator_path', 'page','on_mounted','on_refresh'],

        data: function () {
            return {
                loaded: false,
                planning_entries: [],
                planning_entries_order: localStorage.getItem('planningview_order') ? localStorage.getItem('planningview_order') : 'title',
                planning_entries_orderDirection: localStorage.getItem('planningview_orderDirection') ? localStorage.getItem('planningview_orderDirection') : 'ASC',
                planning_entries_paginator: {},
                selectedEntry: {},
                massEditIds: {},
                quickActionSelector: '',
                massEditStatusValue: '',
                statuses: [],
                statusesWithColor: [],
                users: [],

            }
        },
        mounted : function() {
            if ( typeof this.$props.on_mounted === 'function') {
                this.$props.on_mounted();
            }
        },
      
        methods: {
            hasPermission,
            refresh: function () {
                let filterStatus = localStorage.getItem('planningview_filterstatuses') ? JSON.parse(localStorage.getItem('planningview_filterstatuses')) : [];
                this.loaded = false;
                this.planning_entries = [];
                if (typeof this.$props.id_planning !== 'undefined') {
                    listByPlanning(this.$props.id_planning, {
                        order: this.planning_entries_order,
                        orderDirection: this.planning_entries_orderDirection,
                        page: this.$props.page,
                        id_status: filterStatus,
                    }).then((response) => {
                        this.planning_entries = response.planning_entry;
                        this.planning_entries_paginator = response.paginator;
                        this.loaded = true;
                        this.$forceUpdate();

                    });
                }
                else {
                    list({
                        order: this.planning_entries_order,
                        orderDirection: this.planning_entries_orderDirection,
                        page: this.$props.page,
                        id_status: filterStatus,
                    }).then((response) => {
                        this.planning_entries = response.planning_entry;
                        this.planning_entries_paginator = response.paginator;
                        this.loaded = true;
                        this.$forceUpdate();
                    });
                }

                listStatuses().then((response) => {
                    this.statuses = response;
                });
                listStatusesWithColor().then((response) => {
                    this.statusesWithColor = response;
                });

                listUsers().then((response) => {
                    this.users = response;
                });
                if ( typeof this.$props.on_refresh === 'function') {
                    this.$props.on_refresh();
                }

            },
            descriptionPlanningEntryModal: function (entry) {
                this.selectedEntry = entry;
                this.$refs.viewEntryDescriptionModal.show();
            },
            setOrder: function (field) {
                var orderDir = 'ASC';
                if (field === this.planning_entries_order) {
                    orderDir = this.planning_entries_orderDirection == 'ASC' ? 'DESC' : 'ASC';
                }
                this.planning_entries_order = field;
                this.planning_entries_orderDirection = orderDir;

                localStorage.setItem('planningview_order', field);
                localStorage.setItem('planningview_orderDirection', orderDir);

                this.refresh();
            },
            deleteEntryItem: function (id) {
                if (confirm('Confirmez que vous voulez supprimer l\'entrée de planning')) {
                    deleteItem(id).then(() => {
                        this.refresh();
                    })
                }
            },
            canFollow(entry) {
                if (getLoggedUser().id == entry.id_creator) {
                    return hasPermission('planning_entry', 'update_self');
                } else {
                    return hasPermission('planning_entry', 'update');
                }
            },
            toggleFollow(id_entry) {
                toggleFollow(id_entry, getLoggedUser().id).then(() => {
                    this.refresh();
                });
            },
            massEditAll(event) {
                this.massEditIds = {};
                if (event.target.checked) {
                    for (let i = 0; i < this.planning_entries.length; i++) {
                        var entry = this.planning_entries[i];
                        this.massEditIds[entry.id] = entry.id;
                    }
                }
            },
            toogleMassEdit(id) {
                if (typeof this.massEditIds[id] !== 'undefined') {
                    delete this.massEditIds[id];
                }
                else {
                    this.massEditIds[id] = id;
                }
                setTimeout(() => {
                    this.$forceUpdate();
                }, 1)

            },
            massEditStatus() {
                let status = this.massEditStatusValue;
                let ids = Object.keys(this.massEditIds);
                massUpdate(ids, 'id_status', status).then(() => {
                    this.refresh();
                });
            },
        },
        components: {
            EditableText,
            EditableList,
            EditableDateTime,
            Paginator,
            PlanningEntryDescription,
            TimeRemain,
            Modale,
            MenuDownIcon,
            MenuUpIcon,
            DeleteIcon,
            BellIcon,
            BellOffIcon,
            DotsHorizontalIcon
        },
        watch: {
            page: function (newVal, oldVal) { // watch it
                this.refresh();
            }
        }


    }
</script>