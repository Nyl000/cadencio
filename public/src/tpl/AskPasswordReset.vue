<template>
    <div class="login">
        <form v-on:submit.prevent="askReset">
            <div class="loginbox reset">
                <div class="logowrap">
                    <img class="logo" src="/resources/images/logo.png" alt="Cadencio"/>
                </div>
                <div v-if="!sent">
                    <p class="helptext">
                        Please enter your email. If a user match with it, you will receive a link in your mailbox to reset your password.
                    </p>
                    <input type="text" v-model="username" placeholder="your@email.com"/>
                    <button type="submit" class="send">Send</button>
                </div>
                <info-message type="success" v-bind:message="successMessage" ref="info"/>
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
				username: '',
				password: '',
				successMessage: '',
				sent: false,
			}
		},
		methods: {
			askReset: function () {

				Rest.request('/user/reset', 'POST', {
					email: this.$data.username,
				}).then(
					() => {
						this.sent = true;
						this.successMessage = "If the email match with an account, you will receive a reset password email soon ! ";
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

