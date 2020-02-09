<template>
    <div class="users_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addRoleModal"><plus-icon /> Add</button>
                <button class="button" v-on:click="refreshGrid()"><sync-icon /></button>
            </div>
            <entity-table ref="table" :model="rolesModel" :definition="tableDefinition" :page="this.$route.params.page || 1" />

        </div>
        <Modale ref="addRoleModal">
            <RoleAdd v-bind:onAdded="onRoleAdded"/>
        </Modale>
        <Modale ref="managePermissionsModal">
            <RoleManagePermissions :role="selectedRoleId" />
        </Modale>
    </div>
</template>

<script>

    import {hasPermission} from 'js/Models/User';

    import Modale from 'tpl/Ui/Modale.vue';
    import RoleAdd from 'tpl/RoleAdd.vue';
    import RoleManagePermissions from 'tpl/RoleManagePermissions.vue';
    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import PlusIcon from 'vue-material-design-icons/Plus.vue';
    import EntityTable from 'tpl/Ui/EntityTable';
    import {deleteIcon, settingIcon} from 'js/Services/SvgIcons.js';

    const rolesModel = require('js/Models/Role');

    export default {
        data: function()  {
            return {
                page: 1,
                rolesModel : rolesModel,
                selectedRoleId : null,
                tableDefinition : {
                    idField: 'id',
                    saveurl:'/roles/{id}',
                    columns : [
                        {property: 'name', label : 'Name', sortable : true, renderer : {
                            type : 'EditableText',
                            placeholder: 'Name',
                            canUpdate : hasPermission('roles','update'),
                        }},
                        {property: 'label', label : 'Label', sortable : true, renderer : {
                            type : 'EditableText',
                            placeholder: 'Label',
                            canUpdate : hasPermission('roles','update'),
                        }},
                    ],
                    actions : [
                        { callback : this.managePermissionsModal, html : settingIcon, canDisplay : hasPermission('roles','update')  },
                        { callback : this.deleteItem, html : deleteIcon, canDisplay : hasPermission('roles','delete')  },
                    ]
                },
            }
        },
        mounted: function () {

            this.refreshGrid();

        },
        methods: {
            hasPermission: (resource, action) => {
                return hasPermission(resource, action);
            },
            deleteItem: function (userId) {
                if (confirm('Confirmez que vous voulez supprimer le role')) {
                    deleteItem(userId).then(() => {
                        this.refreshGrid();
                    })
                }
            },
            refreshGrid: function () {
                this.$refs.table.refresh();
            },
            addRoleModal: function () {
                this.$refs.addRoleModal.show();
            },
            onRoleAdded: function () {
                this.$refs.addRoleModal.hide();
                this.refreshGrid();
            },
            managePermissionsModal: function (idRole) {
                this.selectedRoleId = idRole;
                this.$refs['managePermissionsModal'].show();
            },

        },
        watch: {
            '$route':function(newParam, oldParam) {
        if (newParam.params.page !== oldParam.params.page) {
            this.refreshGrid();
        }

    }
    },

    components: {
        Modale,
        RoleAdd,
        RoleManagePermissions,
        SyncIcon,
        PlusIcon,
        EntityTable
        }
    }

</script>