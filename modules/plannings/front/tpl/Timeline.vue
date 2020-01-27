<template>
    <div>
        <div class="timeline" v-if="canDisplay">
            <div class="zoom">
                Time steps
                <select v-model="zoom">
                    <option value="current_day">Current Day</option>
                    <option value="current_week">Current Week</option>
                    <option value="current_month">Current Month</option>
                    <option value="full">All Planning</option>

                </select>
            </div>
            <div class="inner" v-dragscroll>
                <div class="measure">
                    <div v-bind:class="{measureItem:true, timeDelimiter:index % 5 == 1}" v-for="index in sizeInUnit+1"
                         :key="index" v-bind:style="{width: unitSize+'%' }">
                        <div class="label top">
                            {{moment(new Date(startDate.getTime() + ((index-1) * unitDivider * 1000 ))).format(formatMeasure)}}
                        </div>
                        <div class="label">
                            {{moment(new Date(startDate.getTime() + ((index-1) * unitDivider * 1000 ))).format(formatMeasure)}}
                        </div>
                    </div>
                    <div class="now" v-bind:style="{left: getNowPosition()+'%'}">
                        <span class="label">Now</span>
                    </div>
                </div>
                <div class="entries">
                    <div class="entry" v-for="entry in entries"
                         v-bind:style="{ backgroundColor: entry.color ,left: getLeftPosition(entry)+'%', width: getEntrySize(entry)+'%'  }" :title="entry.title" v-on:click="editPlanningEntryModal(entry)">
                        <strong>
                            {{entry.title}}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="timeline" v-if="!canDisplay">
            <div class="no_entries">No entry to display in timeline</div>
        </div>
        <Modale ref="editPlanningEntryModal">
            <PlanningEntryAdd v-bind:onAdded="onPlanningEntrySaved" :entry="selectedEntry"/>
        </Modale>
    </div>
</template>


<script>

    import { dragscroll } from 'vue-dragscroll'


    import {getTimelineByPlanning, deleteItem} from 'js/Models/PlanningEntry';
    import {utcToLocaleTime,dateToLocaleTime,getMonday} from 'js/Services/Utils';
    import moment from 'moment-timezone';
    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableList from 'tpl/Ui/EditableList.vue';
    import EditableDateTime from 'tpl/Ui/EditableDateTime.vue';
    import {hasPermission} from 'js/Models/User';
    import {selectList as listStatuses}  from 'js/Models/PlanningStatus';
    import Modale from 'tpl/Ui/Modale.vue';
    import PlanningEntryAdd from 'tpl/PlanningEntryAdd.vue'

    export default {
        props: ['id_planning'],
        data: () => {
            return {
                sizeInSeconds: 0,
                sizeInUnit: 0,
                unitDivider: 60 * 60 * 1000,
                unitSize: 5,
                startDate: new Date(),
                entries: [],
                statuses: [],
                formatMeasure : 'DD/MM HH:mm',
                canDisplay: false,
                zoom: localStorage.getItem('timeline_zoom') ? localStorage.getItem('timeline_zoom') : 'full',
                selectedEntry : undefined,

            }
        },
        mounted: function () {
            this.refreshModule();
        },
        methods: {
            hasPermission: hasPermission,
            utcToLocaleTime: utcToLocaleTime,
            moment: moment,

            editPlanningEntryModal: function (entry) {
                this.selectedEntry = entry;
                this.$refs.editPlanningEntryModal.show();
            },

            getNowPosition: function () {
                let now = dateToLocaleTime(new Date());
                let diffInSeconds = (now.getTime() - this.startDate.getTime()) / 1000;
                return diffInSeconds / this.unitDivider * this.unitSize;

            },
            getLeftPosition: function (entry) {
                let dateStartEntry = utcToLocaleTime(entry.date_start);
                let diffInSeconds = (dateStartEntry.getTime() - this.startDate.getTime()) / 1000;
                return diffInSeconds / this.unitDivider * this.unitSize;

            },
            onPlanningEntrySaved : function() {
                this.refreshModule();
                this.$refs.editPlanningEntryModal.hide();

            },
            getEntrySize: function (entry) {
                let dateStartEntry = utcToLocaleTime(entry.date_start);
                let dateEndEntry = utcToLocaleTime(entry.date_end);
                let diffInSeconds = (dateEndEntry.getTime() - dateStartEntry.getTime()) / 1000;
                return diffInSeconds / this.unitDivider * this.unitSize;

            },

            setFirstDate : function(differenceInSeconds) {
                let date = this.startDate;
                date.setHours(0);
                date.setSeconds(0);
                date.setMinutes(0);


                if (this.zoom == 'current_week') {
                    date.setDate(getMonday(date).getDate());
                }
                if (this.zoom == 'current_month') {
                    date.setDate(1);
                }

                this.startDate = date;


            },
            setAutosScale: function (differenceInSeconds) {

                if(this.zoom == 'current_day') {
                    this.unitDivider = 60 * 60;
                    this.unitSize = 100/24;
                    this.formatMeasure = 'HH:mm';
                    differenceInSeconds = Math.max(differenceInSeconds , 60*60*24);
                    this.sizeInUnit = Math.ceil(differenceInSeconds / this.unitDivider);

                }
                else if(this.zoom == 'current_week') {
                    this.unitDivider = 60 * 60 * 24;
                    this.unitSize = 100/7;
                    this.formatMeasure = 'dddd MM/DD';
                    differenceInSeconds = Math.max(differenceInSeconds , 60*60*24*7);
                    this.sizeInUnit = Math.ceil(differenceInSeconds / this.unitDivider);


                }
                else if(this.zoom == 'current_month') {
                    this.unitDivider = 60 * 60 * 24 ;
                    this.unitSize = 100/31;
                    this.formatMeasure = 'MM/DD';
                    differenceInSeconds = Math.max(differenceInSeconds , 60*60*24*31);
                    this.sizeInUnit = Math.ceil(differenceInSeconds / this.unitDivider);

                }
                else {
                    this.unitDivider = differenceInSeconds / 20;
                    this.formatMeasure = 'MM/DD HH:mm';
                    this.sizeInUnit = Math.ceil(this.sizeInSeconds / this.unitDivider);

                }

                this.setFirstDate();
            },
            refreshModule: function () {

                let filterStatus = localStorage.getItem('planningview_filterstatuses') ? JSON.parse(localStorage.getItem('planningview_filterstatuses')) : [];
                this.entries = [];

                getTimelineByPlanning(this.id_planning, {id_status: filterStatus}).then((response) => {
                    let dateStart = utcToLocaleTime(response.first_date);
                    let dateEnd = utcToLocaleTime(response.last_date);

                    let differenceInSeconds = (dateEnd.getTime() - dateStart.getTime()) / 1000;


                    this.sizeInSeconds = differenceInSeconds;
                    this.startDate = dateStart;
                    this.setAutosScale(differenceInSeconds);

                    this.entries = response.entries;
                    if (this.entries.length > 0) {
                        this.canDisplay = true;
                    }
                });
                listStatuses().then((response) => {
                    this.statuses = response;
                })
            },
            deleteEntryItem: function (id) {
                if (confirm('Confirmez que vous voulez supprimer l\'entrée de planning')) {
                    deleteItem(id).then(() => {
                        this.refreshModule();
                    })
                }
            },
        },
        components: {EditableText, EditableList, EditableDateTime,Modale,PlanningEntryAdd},
        watch: {
            'zoom':function(newParam, oldParam) {
                this.refreshModule();
                localStorage.setItem('timeline_zoom',newParam);
            }
        },
        directives : {
            'dragscroll': dragscroll
        }

    }
</script>