<template>
    <div class="manage-permission-dialog" v-if="roleObject">
        <md-dialog-content>
            <md-content class="md-scrollbar">
                <md-list :md-expand-single="false">
                    <md-list-item md-expand :md-expanded="true" :key="key" v-for="(resource,key) in allPermissions">
                        <span class="md-list-item-text">{{key}}</span>
                        <md-list slot="md-expand">
                            <md-list-item v-for="(right ,index) in resource" :key="index" class="md-inset role-option">
                                <md-checkbox v-model="userPermissions[key][index]"
                                             @change="togglePermission(key,right)"/>
                                {{right}}
                            </md-list-item>
                        </md-list>
                    </md-list-item>
                </md-list>
            </md-content>
        </md-dialog-content>
    </div>
</template>

<script>

    import {getOne, getAllPermissions, addPermission, deletePermission} from 'js/Models/Role';

    export default {
        props: ['role'],

        data: function () {
            return {
                roleObject: null,
                allPermissions: {},
                userPermissions :{}

            }
        },
        mounted: function () {

            this.refresh();
        },
        methods: {
            refresh: async function () {
                this.roleObject = await getOne(this.role);
                this.allPermissions = await getAllPermissions();
                let userPermissions = JSON.parse(JSON.stringify(this.allPermissions));
                for (let i in userPermissions) {
                    userPermissions[i].forEach((j,index) => {
                        userPermissions[i][index] = this.ownPermissions(i + '.' + j) ? true : false;
                    })

                }
                this.userPermissions = userPermissions

            },
            ownPermissions: function (resource) {
                return this.roleObject.permissions.indexOf(resource) >= 0 ? 1 : 0;

            },
            togglePermission: function (resource, right) {
                if (!this.ownPermissions(resource + '.' + right)) {
                    addPermission(this.role, resource, right).then(() => {
                        this.refresh();
                    });
                } else {
                    deletePermission(this.role, resource, right).then(() => {
                        this.refresh();
                    })
                }
            }

        },
    }

</script>