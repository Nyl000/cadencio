<template>
    <transition name="fadedown">
        <div :class="'toast '+state" v-if="visible">
            {{message}}
        </div>
    </transition>

</template>

<script>
    export default {
        data: function () {
            return {
                message: '',
                visible: false,
                state : '',
            }
        },
        methods: {
            show(message, state, delaySeconds) {
                this.message = message;
                this.visible = true;
                this.state = state;
                setTimeout(() => {
                    this.visible = false
                }, delaySeconds * 1000);
            }
        }
    }
</script>

