<template>
    <transition name="fade">
        <div v-if="visible" class="modale">
            <div class="overlay">
                <div class="modalbox">
                    <div class="close" v-on:click="hide">
                        <close-icon />
                    </div>
                    <div class="content">
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import CloseIcon from 'vue-material-design-icons/Close.vue';

    export default {
        props: ['message'],
        data: () => {
            return {
                visible: false,
            }
        },
        methods: {

            show: function () {
                this.$data.visible = true;

            },

            hide: function () {
                this.$data.visible = false;
            }
        },
        components : {CloseIcon}
    }
</script>