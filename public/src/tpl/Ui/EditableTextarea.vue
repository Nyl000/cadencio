<template>
    <transition name="fade">
        <div class="editable">

            <div>
                {{val.split(' ').splice(0,9).join(' ')}} <a href="javascript:void(0)" v-show="!editMode" @click="showEditModale = true">[...]</a>
            </div>
            <check-bold-icon v-if="success" class="icon success" />
            <close-icon v-if="error" class="icon error" />
            <md-dialog :md-active.sync="showEditModale">
                <md-dialog-content>
                    <md-field class="description-field">
                        <label>{{$t('Content')}}</label>
                        <md-textarea md-autogrow
                                     v-model="val"></md-textarea>
                    </md-field>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-primary md-raised"
                               v-on:click="editDone">
                        {{$t('Update')}}
                    </md-button>
                </md-dialog-actions>
            </md-dialog>
        </div>
    </transition>
</template>


<script>
    import Rest from 'js/Services/Rest';
    import CheckBoldIcon from 'vue-material-design-icons/CheckBold.vue';
    import CloseIcon from 'vue-material-design-icons/Close.vue';
    import LinkVariantIcon from 'vue-material-design-icons/LinkVariant.vue';

    export default {
        props: ['value', 'list', 'saveurl', 'id', 'field', 'canupdate','placeholder','link','callbackSuccess'],
        data: function () {
            return {
                showEditModale : false,
                val: this.value == null ? '' : this.value,
                success: false,
                error: false,
                editMode: false,
            }
        },
        methods: {
            handleType: function($event) {
                if($event.code == 'Enter') {
                    this.leaveEditmode();
                }
            },

            editDone: function() {
                this.error = false;
                let datas = {id: this.id};
                datas[this.field] = this.val;
                if (this.val !== this.value) {
                    Rest.authRequest(this.saveurl, 'POST', datas).then(
                        () => {
                            this.success = true;
                            this.showEditModale = false;
                            setTimeout(() => {
                                this.success = false;
                                this.onCallback();
                            }, 800);
                        },
                        () => {
                            this.error = true;
                            this.showEditModale = false;

                        }
                    );
                }
            },
            onCallback : function() {
                if (typeof this.$props.callbackSuccess !== 'undefined')  {
                    this.$props.callbackSuccess();
                }
            },

        },
        components: {CheckBoldIcon,CloseIcon,LinkVariantIcon}


    }
</script>