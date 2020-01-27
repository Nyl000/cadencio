<template>
    <div>
     <h3>Permissions for role "{{roleObject.label}}"</h3>
        <ul class="permissionList">
            <li v-for="(resource,key) in allPermissions">
                {{key}}
                <ul>
                    <li v-for="right in resource">
                        <input type="checkbox" :checked="ownPermissions(key+'.'+right)" v-on:click.prevent="togglePermission(key,right)" />{{right}}
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</template>

<script>

    import {getOne,getAllPermissions,addPermission,deletePermission} from 'js/Models/Role';

    export default {
        props: ['role'],

        data: function() {
            return {
                roleObject: {},
                allPermissions : {}
            }
        },
        mounted : function() {

         this.refresh();
        },
        methods: {
            refresh : function() {
                getOne(this.role.id).then((roleDistant) => {
                    this.roleObject = roleDistant;
                });
                getAllPermissions().then((permissions) => {
                    this.allPermissions = permissions;
                });
            },
            ownPermissions: function(resource) {
                    return this.roleObject.permissions.indexOf(resource) >= 0 ? 1 : 0;


            },
            togglePermission: function(resource,right) {
                if (!this.ownPermissions(resource+'.'+right)) {
                    addPermission(this.roleObject.id, resource,right).then(() => {
                        this.refresh();
                    });
                }
                else {
                    deletePermission(this.roleObject.id,resource,right).then(() => {
                        this.refresh();
                    })
                }
            }

        },
    }

</script>