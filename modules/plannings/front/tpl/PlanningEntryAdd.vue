<template>
    <div>
        <div class="formInput">
            <label>Status</label>
            <select v-model="id_status">
                <option v-for="status in statuses" :key="status.id" :value="status.id" >
                    {{status.title}}
                </option>
            </select>
        </div>
        <div class="formInput">
            <label>Title</label>
            <input type="text" v-model="title"/>
        </div>
        <div class="formInput">
            <label>Description</label>
            <textarea v-model="description"></textarea>
        </div>
        <div class="formInput">
            <label>Start date</label>
            <Datetime :zone="getUserOption('timezone')" placeholder="Enter a date" type="datetime" v-model="date_start" ></Datetime>

        </div>
        <div class="formInput">
            <label>End date</label>
            <Datetime :zone="getUserOption('timezone')" placeholder="Enter a date" type="datetime" v-model="date_end" ></Datetime>
        </div>
        <button v-bind:disabled="title === ''" class="button success" v-on:click="add">
            {{typeof this.$props.entry === 'undefined' ? 'Add' : 'Save'}}
        </button>
    </div>
</template>

<script>

    import {add} from 'js/Models/PlanningEntry';
    import {list as listStatuses}  from 'js/Models/PlanningStatus';
    import {Datetime} from 'vue-datetime';
    import {getUserOption} from 'js/Models/User';
    import moment from 'moment-timezone';

    export default {
        props: ['onAdded','idPlanning','entry'],

        data: function() {
            if (typeof this.$props.entry !== 'undefined') {
                return {
                    id: this.$props.entry.id,
                    title: this.$props.entry.title,
                    description: this.$props.entry.description,
                    date_start: moment.tz(this.$props.entry.date_start,'UTC').toISOString(),
                    date_end: moment.tz(this.$props.entry.date_end,'UTC').toISOString(),
                    id_status: this.$props.entry.id_status,
                    statuses: [],
                    id_planning: this.$props.entry.id_planning
                }
            }
            else {
                return {
                    id: '',
                    title: '',
                    description: '',
                    date_start: '',
                    date_end: '',
                    id_status: '1',
                    statuses: [],
                    id_planning: this.idPlanning
                }
            }
        },
        mounted: function () {
            listStatuses().then((results) => {
                this.$data.statuses  = results.planning_status;
            })
        },
        methods: {
            getUserOption:getUserOption,
            add : function() {
                add({
                    id: this.$data.id,
                    title: this.$data.title,
                    description : this.$data.description,
                    date_start : moment(this.$data.date_start).format('YYYY-MM-DD HH:mm:ss'),
                    date_end : moment(this.$data.date_end).format('YYYY-MM-DD HH:mm:ss'),
                    id_planning: this.$data.id_planning,
                    id_status : this.$data.id_status
                }).then(() => {
                    this.$props.onAdded();
                })
            }
        },
        components: {Datetime}
    }

</script>