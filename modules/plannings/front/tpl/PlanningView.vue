<template>
    <div class="planning_view">
        <div class="boxcontainer">
            <div class="title">
                <h1>{{planning.title}}</h1>
                <h2>{{planning.closed_entries}} / {{planning.total_entries}} closed</h2>
            </div>
            <div class="actionbar">
                <button class="button button-add" v-on:click="addPlanningEntryModal"><plus-icon />
                    Add a new entry
                </button>
                <button class="button" v-on:click="refresh()"><sync-icon /></button>
                <button class="button" v-on:click="openFilterStatusModale()"
                        v-html="filter_statuses.length == 0 ? 'All Status' : getFilterStatusLabel()">
                </button>
            </div>
            <div class="entry_viewer tab-box">
                <div class="tabs">
                    <button :class="tabSelected == 'list' ? 'active' : ''" v-on:click="setTab('list')">Listing</button>
                    <button :class="tabSelected == 'timeline' ? 'active' : ''" v-on:click="setTab('timeline')">
                        Timeline
                    </button>
                </div>
                <div class="tab-box-content">
                    <div class="tabcontent-list" v-if="tabSelected === 'list' && planning.id !== false">
                        <PlanningGrid ref="gridMod" :page="page" :id_planning="planning.id" :on_mounted="refreshGrid" :on_refresh="refreshPlanning"/>
                    </div>
                    <div class="tabcontent-timeline"
                         v-if="tabSelected === 'timeline' && typeof planning.id !== 'undefined'">
                        <Timeline ref="timelineMod" :id_planning="planning.id"/>
                    </div>
                </div>
            </div>
        </div>
        <Modale ref="addPlanningEntryModal">
            <PlanningEntryAdd v-bind:onAdded="onPlanningEntryAdded" :idPlanning="planning.id"/>
        </Modale>


        <Modale ref="filterModal">
            <div class="filterbox">
                <h2>Select one ore more</h2>
                <ul class="filter">
                    <li v-for="(status,id) in statusesWithColor" v-on:click="toggleStatusFilter(id)" v-bind:key="id">
                        <span :style="{visibility: filter_statuses.indexOf(id) > -1 ? 'visible':'hidden'}"
                              class="choiced"><check-bold-icon /></span> <span class="colorindicator"
                                                                         :style="{backgroundColor: status.color}"></span>
                        {{status.title}}
                    </li>
                </ul>
                <button class="button" v-on:click="closeFilterStatusModale()">
                    OK
                </button>
            </div>
        </Modale>
    </div>
</template>

<script>

    import {view}  from 'js/Models/Planning';
    import {selectList as listStatuses, selectWithColor as listStatusesWithColor}  from 'js/Models/PlanningStatus';
    import Modale from 'tpl/Ui/Modale.vue';
    import PlanningEntryAdd from 'tpl/PlanningEntryAdd.vue';
    import Timeline from 'tpl/Timeline.vue';
    import PlanningGrid from 'tpl/PlanningGrid.vue';

    import PlusIcon from 'vue-material-design-icons/Plus.vue';
    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue';
    import MenuUpIcon from 'vue-material-design-icons/MenuUp.vue';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';
    import CheckBoldIcon from 'vue-material-design-icons/CheckBold.vue';

    export default {
        data: function () {
            return {
                planning: {
                    id : false,
                    title : '',
                    total_entries : 0,
                    closed_entries : 0
                },

                statuses: [],
                statusesWithColor: [],
                users: [],
                filter_statuses: localStorage.getItem('planningview_filterstatuses') ? JSON.parse(localStorage.getItem('planningview_filterstatuses')) : [],
                tabSelected: localStorage.getItem('planningview_tab') ? localStorage.getItem('planningview_tab') : 'list',
                page: this.$route.params.page || 1,

            }
        },
        mounted: function () {
            this.refresh();
        },
        methods: {
            hasPermission: (resource, action) => {
                return hasPermission(resource, action);
            },
            refreshGrid: function () {
                if (typeof this.$refs.gridMod !== 'undefined') {
                    this.$refs.gridMod.refresh();
                }
            },
            refreshPlanning: function () {
                let id = this.$route.params.id;
                view(id).then((response) => {
                    this.$set(this.planning,'id',response.id);
                    this.$set(this.planning,'title',response.title);
                    this.$set(this.planning,'total_entries',response.total_entries);
                    this.$set(this.planning,'closed_entries',response.closed_entries);

                });
            },
            refresh: function () {
                this.refreshPlanning();
                listStatuses().then((response) => {
                    this.statuses = response;
                });
                listStatusesWithColor().then((response) => {
                    this.statusesWithColor = response;
                });

                if (typeof this.$refs.timelineMod !== 'undefined') {
                    this.$refs.timelineMod.refreshModule();
                }
                if (typeof this.$refs.gridMod !== 'undefined') {
                    this.$refs.gridMod.refresh();
                }
            },
            addPlanningEntryModal: function () {
                this.$refs.addPlanningEntryModal.show();
            },
            openFilterStatusModale: function () {
                this.$refs.filterModal.show();
            },
            closeFilterStatusModale: function () {
                this.$refs.filterModal.hide();
                this.refresh();
            },
            onPlanningEntryAdded: function () {
                this.$refs.addPlanningEntryModal.hide();
                this.refresh();
            },
            setTab: function (tab) {
                this.tabSelected = tab;
                localStorage.setItem('planningview_tab', tab);
            },
            toggleStatusFilter: function (id_status) {
                if (this.filter_statuses.indexOf(id_status) > -1) {
                    this.filter_statuses.splice(this.filter_statuses.indexOf(id_status), 1);
                }
                else {
                    this.filter_statuses.push(id_status);
                }

                localStorage.setItem('planningview_filterstatuses', JSON.stringify(this.filter_statuses));

            },
            getFilterStatusLabel() {
                let out = [];
                for (let i = 0; i < this.filter_statuses.length; i++) {
                    let status = this.statusesWithColor[this.filter_statuses[i]];
                    if (typeof status !== 'undefined') {
                        out.push('<span class="colorindicator insidefilter" style="background-color:' + status.color + '"></span> ' + status.title);
                    }
                }
                return out.join(' ');
            },
        },
        watch: {
            '$route': function (newParam, oldParam) {
                if (newParam.params.page !== oldParam.params.page) {
                    this.page = newParam.params.page;
                }
                this.refresh();

            }
        },

        components: {
            Modale,
            PlanningEntryAdd,
            Timeline,
            PlanningGrid,
            PlusIcon,
            SyncIcon,
            CheckBoldIcon
        }
    }

</script>