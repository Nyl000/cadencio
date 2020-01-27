<template>
    <transition name="fade">
        <div class="editable">
            <div>
                <swatches swatch-size="25" :show-fallback="true"  :disabled="!canupdate" v-model="val" @close="leaveEditmode"></swatches>
            </div>
            <check-bold-icon v-if="success" class="icon success" />
            <close-icon v-if="error" class="icon error" />
        </div>
    </transition>
</template>


<script>
    import Rest from 'js/Services/Rest';
    import Swatches from 'vue-swatches'
    import CheckBoldIcon from 'vue-material-design-icons/CheckBold.vue';
    import CloseIcon from 'vue-material-design-icons/Close.vue';
    export default {
        props: ['value', 'list', 'saveurl', 'id', 'field', 'canupdate','placeholder','link'],
        data: function () {
            return {
                editMode: false,
                val: this.value == null ? '' : this.value,
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
                this.editMode = false;
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
        components: {Swatches,CheckBoldIcon,CloseIcon}


    }
</script>