<template>
    <div class="home">
        <div class="boxcontainer">
            <div class="title">
                <h1>All planning entries</h1>
            </div>
            <div class="actionbar">
                <button class="button" v-on:click="refreshPlanning()"><i class="fas fa-sync"></i></button>
                <button class="button" v-on:click="openFilterStatusModale()" v-html="filter_statuses.length == 0 ? 'All Status' : getFilterStatusLabel()">

                </button>
            </div>
            <div class="entry_viewer tab-box">
                <div class="tab-box-content">
                    <PlanningGrid ref="gridMod" :page="page" paginator_path="paged_planning_home"  />
                </div>
            </div>

        </div>
        <Modale ref="filterModal">
            <div class="filterbox">
                <h2>Select one ore more</h2>
                <ul class="filter">
                    <li v-for="(status,id) in statusesWithColor" v-on:click="toggleStatusFilter(id)" v-bind:key="id" >
                        <span :style="{visibility: filter_statuses.indexOf(id) > -1 ? 'visible':'hidden'}" class="choiced fas fa-check"></span> <span class="colorindicator" :style="{backgroundColor: status.color}"></span> {{status.title}}
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

    import {hasPermission} from 'js/Models/User';
    import {selectList as listStatuses, selectWithColor as listStatusesWithColor}  from 'js/Models/PlanningStatus';
    import Modale from 'tpl/Ui/Modale.vue';
    import PlanningGrid from 'tpl/Ui/PlanningGrid.vue';

    export default {
        data: function()  {

            return {
                statuses: [],
                statusesWithColor : [],
                filter_statuses : localStorage.getItem('planningview_filterstatuses') ? JSON.parse(localStorage.getItem('planningview_filterstatuses')) : [],
                page : this.$route.params.page || 1,
            }
        },
        mounted: function () {
            this.refreshPlanning();
        },
        methods: {
            hasPermission : (resource,action) => {
                return hasPermission(resource, action);
            },
            openFilterStatusModale: function () {
                this.$refs.filterModal.show();
            },
            refreshPlanning: function () {

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
            closeFilterStatusModale: function () {
                this.$refs.filterModal.hide();
                this.refreshPlanning();
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
                for(let i = 0; i< this.filter_statuses.length; i++) {
                    let status = this.statusesWithColor[this.filter_statuses[i]];
                    if (typeof status !== 'undefined') {
                        out.push('<span class="colorindicator insidefilter" style="background-color:' + status.color + '"></span> ' + status.title);
                    }
                }
                return out.join(' ');
            },
        },
        components: {
            Modale,
            PlanningGrid
        },
        watch:  {
            '$route': function (newParam, oldParam) {
                if (newParam.params.page !== oldParam.params.page) {
                    this.page = newParam.params.page;
                }
            },
        }
    }

</script>