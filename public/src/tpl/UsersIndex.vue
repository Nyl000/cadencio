<template>
    <div class="users_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addUserModal">
                    <plus-icon/>
                    Add
                </button>
                <button class="button button-secondary " v-on:click="refreshGrid()">
                    <sync-icon/>
                </button>

            </div>
            <div class="tablewrapper">
                <entity-table name="tableusers" ref="table" :model="userModel" :definition="tableDefinition"
                              :page="this.$route.params.page || 1"/>

            </div>
            <Modale ref="addUserModale">
                <UserAdd v-bind:onAdded="onUserAdded"/>
            </Modale>
        </div>
    </div>
</template>

<script>

    import {hasPermission} from 'js/Models/User';
    import {selectList as selectListRole}  from 'js/Models/Role';

    import Modale from 'tpl/Ui/Modale.vue';
    import UserAdd from 'tpl/UserAdd.vue';

    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import PlusIcon from 'vue-material-design-icons/Plus.vue';

    import EntityTable from 'tpl/Ui/EntityTable';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';
    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableList from 'tpl/Ui/EditableList.vue';
    import EditablePassword from 'tpl/Ui/EditablePassword.vue';
    const userModel = require('js/Models/User');


    export default {
        data: function () {
            return {
                roles: {},
                userModel: userModel,
                tableDefinition: {},
                page: 1
            }
        },
        mounted: function () {
            this.refreshTableDatas();
            this.refreshGrid();
        },
        methods: {

            //We need a refresh function to keep choices list up to date in case of rest refresh.
            refreshTableDatas: function () {
                let self = this;
                this.tableDefinition = {
                    idField: 'id',
                    title : 'Users',
                    saveurl: '/users/{id}',
                    columns: [
                        {
                            property: 'email', label: 'Email', sortable: true, renderer: {
                            type: EditableText,
                            placeholder: 'Email',
                            canUpdate: hasPermission('users', 'update'),
                        }
                        },
                        {
                            property: 'id_role', label: 'Role', sortable: true, renderer: {
                            type: EditableList,
                            list: self.roles,
                            placeholder: 'Role',
                            canUpdate: hasPermission('users', 'update'),
                        }
                        },
                        {
                            property: 'password', label: 'Password', sortable: false, renderer: {
                            type: EditablePassword,
                            placeholder: 'Password',
                            canUpdate: hasPermission('users', 'update'),
                        }
                        },
                        {
                            property: 'name', label: 'Name', sortable: true, renderer: {
                            type: EditableText,
                            placeholder: 'Name',
                            canUpdate: hasPermission('users', 'update'),
                        }
                        },
                        {
                            property: 'firstname', label: 'First Name', sortable: true, renderer: {
                            type: EditableText,
                            placeholder: 'First Name',
                            canUpdate: hasPermission('users', 'update'),
                        }
                        },
                        {
                            property: 'nickname', label: 'Display Name', sortable: true, renderer: {
                            type: EditableText,
                            placeholder: 'Display Name',
                            canUpdate: hasPermission('users', 'update'),
                        }
                        },
                    ],
                    actions: [
                        { action : self.deleteItem, component : DeleteIcon, canDisplay : hasPermission('users','delete')  },
                ]
                };
            },
            hasPermission: hasPermission,
            deleteItem: function (user) {
                if (confirm('Please confirm the user deletion')) {
                    deleteItem(user.id).then(() => {
                        this.refreshGrid();
                    })
                }
            },
            refreshGrid: function () {
                this.$refs.table.refresh();

                selectListRole().then((roles) => {
                    this.roles = roles;
                    this.refreshTableDatas();
                    this.$forceUpdate();
                });
            },
            addUserModal: function () {
                this.$refs.addUserModale.show();
            },
            onUserAdded: function () {
                this.$refs.addUserModale.hide();
                this.refreshGrid();
            }

        },

        components: {
            Modale,
            UserAdd,
            SyncIcon,
            PlusIcon,
            EntityTable
        }
    }

</script>