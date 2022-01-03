<template>
    <span>
        <help-icon/>
        <md-tooltip md-direction="top">
            <slot></slot>
        </md-tooltip>
    </span>

</template>

<script>
    import HelpIcon from 'vue-material-design-icons/HelpCircle.vue';

    export default {
        components: {HelpIcon}
    }
</script>