<template>
    <div>
        <div class="formInput">
            <input type="password" v-model="password" v-show="false"/>

            <label>{{$t('Email')}}</label>
            <input type="email" v-model="email"/>
        </div>
        <div class="formInput">
            <label>{{$t('Password')}}</label>
            <input type="password" v-model="password" autocomplete="new-password" />
        </div>
        <div class="formInput">
            <label>{{$t('Role')}}</label>
            <select v-model="id_role">
                <option v-for="(item,key) in roles" v-bind:value="key">
                    {{item}}
                </option>
            </select>
        </div>
        <button v-bind:disabled="title === ''" class="button success" v-on:click="add">
            {{$t('Add')}}
        </button>
        <toast ref="toast"/>
    </div>
</template>

<script>

    import {add} from 'js/Models/User';
    import {selectList as selectListRole}  from 'js/Models/Role';
    import Toast from 'tpl/Ui/Toast';

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
            add : async function() {
                try {

                    await add({
                        email: this.$data.email,
                        password : this.$data.password,
                        id_role : this.$data.id_role,
                    });
                    this.$props.onAdded();
                }
                catch (error) {
                    this.$refs.toast.show(this.$t(error.api_error), 'error', 5);

                }
            }
        },
        components: {Toast}

    }

</script>