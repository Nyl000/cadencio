<template>
    <div>
        <div class="formInput">
            <input type="password" v-model="password" v-show="false"/>

            <label>Email</label>
            <input type="email" v-model="email"/>
        </div>
        <div class="formInput">
            <label>Password</label>
            <input type="password" v-model="password" autocomplete="new-password" />
        </div>
        <div class="formInput">
            <label>Role</label>
            <select v-model="id_role">
                <option v-for="(item,key) in roles" v-bind:value="key">
                    {{item}}
                </option>
            </select>
        </div>
        <button v-bind:disabled="title === ''" class="button success" v-on:click="add">
            Add
        </button>
    </div>
</template>

<script>

    import {add} from 'js/Models/User';
    import {selectList as selectListRole}  from 'js/Models/Role';

    export default {
        props: ['onAdded'],

        data: () => {
            return {
                email: '',
                password:'',
                roles: [],
                id_role : '',
            }
        },
        mounted : function() {
            selectListRole().then((roles) => {
                this.roles = roles;
            });
        },
        methods: {
            add : function() {
                add({
                    email: this.$data.email,
                    password : this.$data.password,
                    id_role : this.$data.id_role
                }).then(() => {
                    this.$props.onAdded();
                })
            }
        },
    }

</script>