<template>
    <div class="login">
        <form v-on:submit.prevent="attemptLogin">
            <div class="loginbox">
                <div class="logowrap">
                    <img class="logo" :src="getLogo()" alt="Cadencio"/>
                </div>
                <input type="text" v-model="username" placeholder="your@email.com"/>
                <input type="password" v-model="password" placeholder="password"/>
                <button v-on:click="attemptLogin" class="send"><small-loader v-if="loading" /> {{$t('Login')}}</button>
                <info-message type="error" v-bind:message="errorMessage" ref="info"/>
                <router-link to="/passwordreset">
                    {{$t('I forgot my password')}}
                </router-link>
            </div>
        </form>
    </div>
</template>

<script>
    import InfoMessage from 'tpl/Ui/InfoMessage.vue';
    import SmallLoader from 'tpl/Ui/SmallLoader';
    import {getHooks} from 'js/Services/HookHandler';

    const modulesModel = require('js/Models/Module');

    export default {

        data: () => {
            return {
                username: '',
                password: '',
                errorMessage: '',
                loading: false,
            }
        },
        methods: {
            getLogo : function() {

                let logopath = '/resources/images/logo.png';
                let logoHooks = getHooks('override_login_logo');

                logoHooks.forEach((hook) => {
                    logopath = hook(logopath);
                });

                return logopath;
            },
            attemptLogin: async function () {
                try {
                    this.loading = true;

                    await this.$store.dispatch('login/loginAsync', {
                        username: this.username,
                        password: this.password,
                    });

                    await this.$store.dispatch('login/refreshUserInfosAsync');

                    modulesModel.refreshActivesModules(() => {
                        window.location = '/';
                    })
                }
                catch(errorMessage) {
                    this.errorMessage = this.$t(errorMessage);
                    this.$refs.info.show();
                    this.loading = false;
                }
            }
        },
        components: {
            InfoMessage,
            SmallLoader
        }
    }
</script>