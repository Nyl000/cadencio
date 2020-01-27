<template>
    <transition name="fade">
        <div v-if="visible" v-bind:class="'box '+type">
            {{message}}
        </div>
    </transition>
</template>


<script>

    export default {
        props: ['message', 'duration','type'],
        data: () => {
            return {
                visible: false,
            }
        },
        methods: {

            show: function () {
                let duration = this.$props.duration || -1;
                this.$data.visible = true;
                if (duration >= 0) {
                    setTimeout(() => {
                        this.$data.visible = false;
                    }, duration);
                }
            }
        }
    }
</script>