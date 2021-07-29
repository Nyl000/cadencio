<template>
    <div class="loginscreen md-layout md-size-100 md-alignment-center-center" style="min-height:100vh">
        <div class="md-layout-item md-size-40 md-large-size-30 md-small-size-80 md-xsmall-size-100">
        <form v-on:submit.prevent="askReset">
            <md-card>
                <md-card-header>
                    <div class="logozone">
                        <img class=" logo" :src="getLogo()" alt="Cadencio"/>
                    </div>
                </md-card-header>
                <md-card-content>
                    <div class="md-layout md-gutter">
                        <div class="md-layout-item md-small-size-100">
                            <div v-if="!sent">
                                <p class="helptext">
                                    {{ $t('Please enter your email. If a user match with it, you will receive a link in your mailbox to reset your password.') }}
                                </p>
                                <md-field>
                                    <label for="username">{{$t('my@email.com')}}</label>
                                    <md-input name="username" id="username" autocomplete="username" v-model="username" />
                                </md-field>
                            </div>
                        </div>
                    </div>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-secondary" to="/login">
                            {{$t('Back to Login')}}
                    </md-button>
                    <md-button type="submit" class="md-primary md-raised" :disabled="loading">
                        {{$t('Reset my password')}}
                    </md-button>
                </md-card-actions>
            </md-card>
            <div class="loginbox reset">

                <info-message type="success" v-bind:message="successMessage" ref="info"/>
            </div>
        </form>
        </div>
    </div>
</template>

<script>

	import Rest from 'js/Services/Rest';
	import InfoMessage from 'tpl/Ui/InfoMessage.vue';
    import {getHooks} from 'js/Services/HookHandler';

	export default {
		data: () => {
			return {
				username: '',
				password: '',
				successMessage: '',
				sent: false,
                loading: false,
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
			askReset: function () {
                this.loading = true;
				Rest.request('/user/reset', 'POST', {
					email: this.$data.username,
				}).then(
					() => {
					    this.loading = false;
						this.sent = true;
						this.successMessage = this.$t('If the email match with an account, you will receive a reset password email soon !');
						this.$refs.info.show();
					},
				)
			}
		},
		components: {
			InfoMessage
		}
	}
</script>

