<template>
    <div class="users_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addRoleModal"><plus-icon /> Add</button>
                <button class="button" v-on:click="refreshGrid()"><sync-icon /></button>
            </div>

            <table class="list">
                <tr class="items title">
                    <th class=" information" v-on:click="setOrder('name')">
                        Name
                        <menu-down-icon v-if="order == 'name' && orderDirection == 'ASC'" />
                        <menu-up-icon v-if="order == 'name' && orderDirection == 'DESC'" />
                    </th>
                    <th class=" information" v-on:click="setOrder('label')">
                        Label
                        <menu-down-icon v-if="order == 'label' && orderDirection == 'ASC'" />
                        <menu-up-icon v-if="order == 'label' && orderDirection == 'DESC'" />
                    </th>
                    <th class=" information">
                        Permissions
                    </th>
                    <th>
                        Actions
                    </th>
                </tr>
                <tr class="items" v-for="role in roles" :key="role.id">
                    <td class="titleBlock information">
                        <EditableText v-bind:canupdate="hasPermission('roles','update')" v-bind:value="role.name"
                                      v-bind:saveurl="'/roles/'+role.id" field="name" placeholder="Name"/>
                    </td>
                    <td class="information">
                        <EditableText v-bind:canupdate="hasPermission('roles','update')" v-bind:value="role.label"
                                      v-bind:saveurl="'/roles/'+role.id" field="label" placeholder="Label"/>
                    </td>
                    <td class="information">
                        <a class="button" v-on:click.prevent="managePermissionsModal(role.id)">
                            <settings-transfer-outline-icon /> Manage permissions
                        </a>
                        <Modale :ref="'managePermissionsModal'+role.id">
                            <RoleManagePermissions :role="role" />
                        </Modale>
                    </td>
                    <td>
                        <delete-icon v-if="hasPermission('roles','delete')" class=" delete" title="Delete role"
                           v-on:click="deleteItem(role.id)" />
                    </td>
                </tr>
            </table>
            <paginator :paginator="paginator" />
        </div>
        <Modale ref="addRoleModal">
            <RoleAdd v-bind:onAdded="onRoleAdded"/>
        </Modale>
    </div>
</template>

<script>

    import {hasPermission} from 'js/Models/User';
    import {list,deleteItem}  from 'js/Models/Role';

    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableList from 'tpl/Ui/EditableList.vue';
    import EditableCheckbox from 'tpl/Ui/EditableCheckbox.vue';
    import EditablePassword from 'tpl/Ui/EditablePassword.vue';
    import Paginator from 'tpl/Ui/Paginator.vue';

    import Modale from 'tpl/Ui/Modale.vue';
    import RoleAdd from 'tpl/RoleAdd.vue';
    import RoleManagePermissions from 'tpl/RoleManagePermissions.vue';
    import SettingsTransferOutlineIcon from 'vue-material-design-icons/SettingsTransferOutline.vue';
    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue';
    import MenuUpIcon from 'vue-material-design-icons/MenuUp.vue';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';
    import PlusIcon from 'vue-material-design-icons/Plus.vue';

    export default {
        data: () => {
            return {
                roles: [],
                paginator: {},
                order: 'email',
                orderDirection: 'ASC',
                page: 1
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

                let page = this.$route.params.page || 1;
                list({
                    order: this.order,
                    orderDirection: this.orderDirection,
                    page: page
                }).then((roles) => {
                    this.roles = roles.roles;
                    this.paginator = roles.paginator;
                });

            },
            setOrder: function (field) {
                var orderDir = 'ASC';
                if (field === this.order) {
                    orderDir = this.orderDirection == 'ASC' ? 'DESC' : 'ASC';
                }
                this.order = field;
                this.orderDirection = orderDir;

                this.refreshGrid();
            },
            addRoleModal: function () {
                this.$refs.addRoleModal.show();
            },
            onRoleAdded: function () {
                this.$refs.addRoleModal.hide();
                this.refreshGrid();
            },
            managePermissionsModal: function (idRole) {
                let ref = 'managePermissionsModal'+idRole;
                let modale =this.$refs[ref];
                modale[0].show();
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
        EditableText,
        EditableList,
        EditableCheckbox,
        Modale,
        RoleAdd,
        EditablePassword,
        Paginator,
        RoleManagePermissions,
        SettingsTransferOutlineIcon,
        SyncIcon,
        PlusIcon,
        MenuDownIcon,
        MenuUpIcon,
        DeleteIcon
        }
    }

</script>