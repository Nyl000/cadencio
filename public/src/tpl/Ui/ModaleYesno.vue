<template>
    <Modale ref="modale" class="yesno_modale">
        <div class="message">
            {{message}}
        </div>
        <div class="buttons">
            <button class="button button-no" @click.prevent="choose(0)">No</button>
            <button class="button button-yes" @click.prevent="choose(1)">Yes</button>
        </div>

    </Modale>
</template>

<script>
    import Modale from 'tpl/Ui/Modale.vue';

    export default {
        props: ['message'],
        data: () => {
            return {
                visible: false,
            }
        },
        methods: {

            trigger() {
                this.$refs.modale.show();
            },
            choose(state, triggerHide) {
                if (typeof triggerHide === 'undefined') triggerHide = true;
                this.$emit('choose', state);
                if (triggerHide) this.$refs.modale.hide();
            }

        },
        components: {Modale}
    }
</script>