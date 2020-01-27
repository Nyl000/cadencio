<template>
    <div class="users_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addUserModal"><plus-icon /> Add</button>
                <button class="button" v-on:click="refreshGrid()"><sync-icon/></button>

            </div>
            <div class="tablewrapper">
                <table class="list">
                    <tr class="items title">
                        <th class=" information" v-on:click="setOrder('email')">
                            Email
                            <menu-down-icon v-if="order == 'email' && orderDirection == 'ASC'" />
                            <menu-up-icon v-if="order == 'email' && orderDirection == 'DESC'" />
                        </th>
                        <th class=" information" v-on:click="setOrder('id_role')">
                            Role
                            <menu-down-icon v-if="order == 'id_role' && orderDirection == 'ASC'" />
                            <menu-up-icon v-if="order == 'id_role' && orderDirection == 'DESC'" />
                        </th>
                        <th class=" information">
                            Password
                        </th>
                        <th class=" information" v-on:click="setOrder('name')">
                            Name
                            <menu-down-icon v-if="order == 'name' && orderDirection == 'ASC'" />
                            <menu-up-icon v-if="order == 'name' && orderDirection == 'DESC'" />
                        </th>
                        <th class=" information" v-on:click="setOrder('firstname')">
                            Firstname
                            <menu-down-icon v-if="order == 'firstname' && orderDirection == 'ASC'" />
                            <menu-up-icon v-if="order == 'firstname' && orderDirection == 'DESC'" />
                        </th>
                        <th class=" information" v-on:click="setOrder('nickname')">
                            Display Name
                            <menu-down-icon v-if="order == 'nickname' && orderDirection == 'ASC'" />
                            <menu-up-icon v-if="order == 'nickname' && orderDirection == 'DESC'" />
                        </th>
                        <th>
                            Actions
                        </th>
                    </tr>
                    <tr class="items" v-for="user in users" :key="user.id">
                        <td class="titleBlock information">
                            <EditableText v-bind:canupdate="hasPermission('users','update')" v-bind:value="user.email"
                                          v-bind:saveurl="'/user/'+user.id" field="email" placeholder="Email"/>
                        </td>
                        <td class="information">
                            <EditableList v-bind:canupdate="hasPermission('users','update')" v-bind:list="roles"
                                          v-bind:value="user.id_role" v-bind:saveurl="'/user/'+user.id" field="id_role"
                                          placeholder="Choisissez un rôle"/>
                        </td>
                        <td>
                            <EditablePassword v-bind:canupdate="hasPermission('users','update')"
                                              v-bind:value="user.password"
                                              v-bind:saveurl="'/user/'+user.id" field="password" placeholder=""/>
                        </td>
                        <td>
                            <EditableText v-bind:canupdate="hasPermission('users','update')" v-bind:value="user.name"
                                          v-bind:saveurl="'/user/'+user.id" field="name" placeholder="Name"/>
                        </td>
                        <td>
                            <EditableText v-bind:canupdate="hasPermission('users','update')"
                                          v-bind:value="user.firstname"
                                          v-bind:saveurl="'/user/'+user.id" field="firstname" placeholder="First Name"/>
                        </td>
                        <td>
                            <EditableText v-bind:canupdate="hasPermission('users','update')"
                                          v-bind:value="user.nickname"
                                          v-bind:saveurl="'/user/'+user.id" field="nickname"
                                          placeholder="Display Name"/>
                        </td>
                        <td>
                            <delete-icon v-if="hasPermission('users','delete')" class=" delete" title="Delete user"
                               v-on:click="deleteItem(user.id)" />
                        </td>
                    </tr>
                </table>
            </div>
            <paginator :paginator="paginator"/>
        </div>
        <Modale ref="addUserModale">
            <UserAdd v-bind:onAdded="onUserAdded"/>
        </Modale>
    </div>
</template>

<script>

    import {hasPermission, list, deleteItem} from 'js/Models/User';
    import {selectList as selectListRole}  from 'js/Models/Role';

    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableList from 'tpl/Ui/EditableList.vue';
    import EditableCheckbox from 'tpl/Ui/EditableCheckbox.vue';
    import EditablePassword from 'tpl/Ui/EditablePassword.vue';
    import Paginator from 'tpl/Ui/Paginator.vue';

    import Modale from 'tpl/Ui/Modale.vue';
    import UserAdd from 'tpl/UserAdd.vue';

    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue';
    import MenuUpIcon from 'vue-material-design-icons/MenuUp.vue';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';
    import PlusIcon from 'vue-material-design-icons/Plus.vue';

    export default {
        data: () => {
            return {
                users: [],
                roles: {},
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
                if (confirm('Confirmez que vous voulez supprimer l\'utilisateur')) {
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
                }).then((users) => {
                    this.users = users.users;
                    this.paginator = users.paginator;
                });
                selectListRole().then((roles) => {
                    this.roles = roles;
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
            addUserModal: function () {
                this.$refs.addUserModale.show();
            },
            onUserAdded: function () {
                this.$refs.addUserModale.hide();
                this.refreshGrid();
            }

        },
        watch: {
            '$route': function (newParam, oldParam) {
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
            UserAdd,
            EditablePassword,
            Paginator,
            SyncIcon,
            PlusIcon,
            MenuDownIcon,
            MenuUpIcon,
            DeleteIcon
        }
    }

</script>