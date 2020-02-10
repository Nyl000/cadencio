<template>
    <div class="status_color_wrap" :style="{backgroundColor: item.color}">
        <EditableList v-bind="$props"/>
    </div>
</template>

<script>

    import EditableList from 'tpl/Ui/EditableList.vue';

    export default {

        extends: EditableList,
        props: ['item'],
        components: {EditableList},
    }
</script>