<template>
    <transition name="fade">
        <div class="editable">

            <div>


                <md-datepicker @md-closed="leaveEditmode" :md-model-type="String"  v-model="val" :md-immediately="true" />
                <check-bold-icon v-if="success" class="icon success" />
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
    import {sqlToDate} from 'js/Services/Utils';

    export default {
        props: ['value', 'list', 'saveurl', 'id', 'field', 'canupdate','placeholder','link'],
        data: function () {
            let dateStr = '';

            if(this.value !== null) {
                let date = moment.tz(this.value,getUserOption('timezone'));
                dateStr = date.format('YYYY-MM-DD')
            }

            console.log(this.value);

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
            sqlToDate : sqlToDate,
            leaveEditmode: function () {
                this.editMode = false;
                this.error = false;
                let datas = {id: this.id};

                let newDate = moment.tz(this.val,'UTC');
                datas[this.field] = newDate.isValid() ? newDate.format('YYYY-MM-DD') : null;

                if (this.val !== this.value) {
                    this.$emit('input',  this.val);
                    this.$emit('change', this.val);
                    if (this.saveurl) {
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
            },
            'value': function (newParam, oldParam) {
                let dateStr = null;
                if(newParam !== null) {
                        let date = moment.tz(newParam, getUserOption('timezone'));
                        dateStr = date.format('YYYY-MM-DD');
                }


                this.val = dateStr;

            }
        },
        components: {Datetime, CheckBoldIcon,CloseIcon}


    }
</script>