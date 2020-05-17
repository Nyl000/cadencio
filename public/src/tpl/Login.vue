<template>
    <div class="login">
        <form v-on:submit.prevent="attemptLogin">
            <div class="loginbox">
                <div class="logowrap">
                    <img class="logo" src="/resources/images/logo.png" alt="Cadencio"/>
                </div>
                <input type="text" v-model="username" placeholder="your@email.com"/>
                <input type="password" v-model="password" placeholder="password"/>
                <button v-on:click="attemptLogin" class="send">Login</button>
                <info-message type="error" v-bind:message="errorMessage" ref="info"/>
                <router-link to="/passwordreset">
                    I forgot my password
                </router-link>
            </div>
        </form>
    </div>
</template>

<script>

    import Rest from 'js/Services/Rest';
    import InfoMessage from 'tpl/Ui/InfoMessage.vue';
    import {instance} from 'js/App';
    import base64 from 'base-64';
    import {testToken} from 'js/Models/User';
	const modulesModel = require('js/Models/Module');

    export default {
        data: () => {
            return {
                username: '',
                password: '',
                errorMessage: '',
            }
        },
        methods: {
            attemptLogin: function () {

                Rest.request('/user/login', 'POST', {
                    email: this.$data.username,
                    password: this.$data.password,
                    use_jwt : true,

                }).then(
                    (data) => {
                        if (data.status === 'ok') {
                            let token = base64.encode(this.$data.username + ':' + this.$data.password);
                            localStorage.setItem('token', data.token);
                            testToken(token).then((datas) => {
                                localStorage.setItem('user', JSON.stringify(datas.user));
								modulesModel.refreshActivesModules(() => {
									window.location = '/';
								})
                            });
                        }
                        else {
                            this.errorMessage = "Wrong login/password";
                            this.$refs.info.show();
                        }
                    },
                    (error) => {
                        this.errorMessage = error.message;
                        this.$refs.info.show();
                    }
                )
            }
        },
        components: {
            InfoMessage
        }
    }
</script>