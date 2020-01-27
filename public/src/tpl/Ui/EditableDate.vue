<template>
    <transition name="fade">
        <div class="editable">
            <div v-show="editMode">

                <input type="date" ref="input" v-model="val" v-on:blur="leaveEditmode"/>
            </div>
            <div v-show="!editMode" v-on:click="enterEditmode">
                <div v-if="link">
                    🔗 <a v-bind:href="link">{{val}}</a>
                </div>
                <div v-if="!link">
                    {{val}}
                </div>
                <span class="placeholder" v-if="val.length == 0">{{placeholder}}</span>
                <check-bold-icon v-if="success" class="icon success" />
                <close-icon v-if="error" class="icon error" />

            </div>
        </div>
    </transition>
</template>


<script>
    import Rest from 'js/Services/Rest';
    import CheckBoldIcon from 'vue-material-design-icons/CheckBold.vue';
    import CloseIcon from 'vue-material-design-icons/Close.vue';

    export default {
        props: ['value', 'list', 'saveurl', 'id', 'field', 'canupdate','placeholder','link'],
        data: function () {
            return {
                editMode: false,
                val: this.value,
                success: false,
                error: false
            }
        },
        methods: {
            enterEditmode: function () {
                if (this.canupdate) {
                    this.editMode = true;
                    setTimeout(() => {
                        this.$refs.input.focus();
                    }, 10)
                }
            },
            leaveEditmode: function () {
                this.editMode = false
                this.error = false;
                let datas = {id: this.id};
                datas[this.field] = this.val;
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
            }
        },
        components: {CheckBoldIcon,CloseIcon}

    }
</script>