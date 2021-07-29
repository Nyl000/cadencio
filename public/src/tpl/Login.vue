<template>
    <div class="loginscreen md-layout md-size-100 md-alignment-center-center" style="min-height:100vh">
        <form v-on:submit.prevent="attemptLogin">
            <md-card>
                <md-card-header>
                    <div class="logozone">
                        <img class=" logo" :src="getLogo()" alt="Cadencio"/>
                    </div>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout md-gutter">
                        <div class="md-layout-item md-small-size-100">
                            <md-field>
                                <label for="username">{{$t('Username')}}</label>
                                <md-input name="username" id="username" autocomplete="username" v-model="username" />
                            </md-field>
                            <md-field>
                                <label for="password">{{$t('Password')}}</label>
                                <md-input type="password" name="password" id="password" autocomplete="password" v-model="password" />
                            </md-field>
                        </div>
                    </div>
                    <md-card-actions>
                        <md-button  v-on:click="attemptLogin" class="md-primary" :disabled="loading">
                            <small-loader v-if="loading"/>
                            {{$t('Login')}}
                        </md-button>
                        <router-link to="/passwordreset">
                            <md-button  class="md-secondary">
                            {{$t('I forgot my password')}}
                            </md-button>
                        </router-link>
                    </md-card-actions>
                    <md-snackbar :md-position="center" :md-duration="10000" :md-active.sync="showError" md-persistent>
                        <span>{{errorMessage}}</span>
                    </md-snackbar>
                </md-card-content>
            </md-card>
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
                showError:false,
            }
        },
        methods: {
            getLogo: function () {

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
                } catch (errorMessage) {
                    this.errorMessage = this.$t(errorMessage);
                    this.showError = true;
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