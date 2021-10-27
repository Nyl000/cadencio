<template>
    <div>
        <md-dialog-content>
            <md-field>
                <label for="email">{{$t('Email')}}</label>
                <md-input name="email" id="email" autocomplete="email" v-model="email"/>
            </md-field>
            <md-field>
                <label for="password">{{$t('Password')}}</label>
                <md-input type="password" name="password" id="password" autocomplete="password" v-model="password"/>
            </md-field>
            <md-field>
                <label for="id_role">{{$t('Role')}}</label>
                <md-select v-model="id_role" name="id_role" id="id_role">
                    <md-option v-for="(item,key) in roles" :value="key" :key="key">
                        {{item}}
                    </md-option>
                </md-select>
            </md-field>
        </md-dialog-content>
        <md-dialog-actions>
            <md-button :disabled="email === '' || id_role === '' || password === ''" class="md-primary md-raised"
                       @click="add">
                {{$t('Add')}}
            </md-button>
        </md-dialog-actions>

    </div>
</template>

<script>

    import {add} from 'js/Models/User';
    import {selectList as selectListRole} from 'js/Models/Role';
    import Toast from 'tpl/Ui/Toast';

    export default {
        props: ['onAdded'],

        data: () => {
            return {
                email: '',
                password: '',
                roles: [],
                id_role: '',
            }


        },
        mounted: function () {
            selectListRole().then((roles) => {
                this.roles = roles;
            });
        },
        methods: {
            add: async function () {
                try {

                    await add({
                        email: this.$data.email,
                        password: this.$data.password,
                        id_role: this.$data.id_role,
                    });
                    this.$props.onAdded();
                } catch (error) {
                    this.$refs.toast.show(this.$t(error.api_error), 'error', 5);

                }
            }
        },
        components: {Toast}

    }

</script>