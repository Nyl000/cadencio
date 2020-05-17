<template>
    <div class="login">
        <form v-on:submit.prevent="askReset">
            <div class="loginbox reset">
                <div class="logowrap">
                    <img class="logo" src="/resources/images/logo.png" alt="Cadencio"/>
                </div>
                <div v-if="!sent">
                    <p class="helptext">
                        Please enter a new password
                    </p>
                    <input type="password" v-model="password" placeholder="New password"/>
                    <button v-on:click="askReset" class="send">Reset</button>
                </div>
                <info-message type="success" v-bind:message="successMessage" ref="info"/>
                <info-message type="error" v-bind:message="errorMessage" ref="error"/>

                <router-link to="/login">
                    Back to Login
                </router-link>
            </div>
        </form>
    </div>
</template>

<script>

	import Rest from 'js/Services/Rest';
	import InfoMessage from 'tpl/Ui/InfoMessage.vue';

	export default {
		data: () => {
			return {
				successMessage : '',
				errorMessage : '',
				password: '',
				sent: false,
			}
		},
		methods: {
			askReset: function () {

				Rest.request('/user/resetpassword', 'POST', {
					password: this.$data.password,
					hash : this.$route.params.hash,
				}).then(
					() => {
						this.sent = true;
						this.successMessage = "Your password has been reset ! ";
						this.$refs.info.show();
					},
					() => {
						this.sent = true;
						this.errorMessage = "This link is invalid or expired.";
						this.$refs.error.show();
					}
				)
			}
		},
		components: {
			InfoMessage
		}
	}
</script>