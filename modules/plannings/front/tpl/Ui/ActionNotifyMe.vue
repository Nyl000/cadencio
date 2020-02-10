<template>
    <transition name="fade">
        <span class="icongrid blue"  v-if="item.followed == 1 && canFollow()">
            <bell-icon />
        </span>
        <span class="icongrid"  v-if="item.followed == 0 && canFollow()">
            <bell-off-icon />
        </span>
    </transition>
</template>

<script>

    import ActionTable from 'tpl/Ui/ActionTable.vue';
    import {hasPermission, selectList as listUsers, getLoggedUser} from 'js/Models/User';
    import BellIcon from 'vue-material-design-icons/Bell.vue';
    import BellOffIcon from 'vue-material-design-icons/BellOff.vue';
    export default {
        extends : ActionTable,
        methods :{
            canFollow() {
                if (getLoggedUser().id == this.item.id_creator) {
                    return hasPermission('planning_entry', 'update_self');
                } else {
                    return hasPermission('planning_entry', 'update');
                }
            },
        },
        components: { BellIcon, BellOffIcon}
    }
</script>