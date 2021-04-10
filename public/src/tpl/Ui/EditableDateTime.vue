<template>
    <transition name="fade">
        <div class="editable">

            <div>
                <Datetime :zone="getUserOption('timezone')" :placeholder="this.placeholder" type="datetime" v-model="val" :disabled="!canupdate"></Datetime>&nbsp;<close-icon v-on:click="remove" v-if="canupdate && this.val!== '' && this.val !== null" />
                <check-bold-icon v-if="success" class="icon success" />
                <close-icon v-if="error" class="icon error" />
            </div>
        </div>
    </transition>
</template>


<script>
    import Rest from 'js/Services/Rest';
    import {Datetime} from 'vue-datetime';
    import 'vue-datetime/dist/vue-datetime.css';
    import moment from 'moment-timezone';
    import {getUserOption} from 'js/Models/User';
    import CheckBoldIcon from 'vue-material-design-icons/CheckBold.vue';
    import CloseIcon from 'vue-material-design-icons/Close.vue';

    export default {
        props: ['value', 'list', 'saveurl', 'id', 'field', 'canupdate','placeholder','link'],
        data: function () {
            let dateStr = '';

            if(this.value !== null) {
                let date = moment.tz(this.value,'UTC');
                dateStr = date.toISOString();
            }

            return {
                editMode: false,
                val: dateStr,
                displayVal : moment(this.value).calendar(),
                success: false,
                error: false,
                init: false
            }
        },
        methods: {
            leaveEditmode: function () {
                this.editMode = false
                this.error = false;
                let datas = {id: this.id};
                let newDate = moment.tz(this.val,'UTC');

                datas[this.field] = newDate.isValid() ? newDate.format('YYYY-MM-DD HH:mm:ss') : null;
                if (this.val !== this.value) {
                    Rest.authRequest(this.saveurl, 'POST', datas).then(
                        () => {
                            this.success = true;
                            setTimeout(() => {
                                this.success = false;
                            }, 800);
                        },
                        () => {
                            this.error = true;
                        }
                    );
                }
            },
            remove : function() {
                this.val = null;
            },
            getUserOption:getUserOption
        },
        watch: {
            'val': function (newParam, oldParam) {
                let oldDate = moment(oldParam).toDate().getTime();
                let newDate = moment(newParam).toDate().getTime();
                if (newParam!= '' && oldDate != newDate) {
                    this.leaveEditmode();
                }
                if (!this.$data.init) {
                    this.$data.init = true;
                }
            }
        },
        components: {Datetime, CheckBoldIcon,CloseIcon}


    }
</script>