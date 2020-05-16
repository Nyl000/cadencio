<template>
    <div class="settingsindex">
        <div class="columns">
            <div class="medium6">
                <div class="roundedbox">
                    <div class="topbar">
                        <h2>Email Settings</h2>
                    </div>
                    <div class="inner">
                        <h3>SMTP</h3>
                        <div class="formInput">
                            <label>Host</label>
                            <input type="text" v-model="mail_smtp_host"/>
                        </div>
                        <div class="formInput">
                            <label>Port</label>
                            <input type="text" v-model="mail_smtp_port"/>
                        </div>
                        <div class="formInput">
                            <label>Username</label>
                            <input type="text" v-model="mail_smtp_user"/>
                        </div>
                        <div class="formInput">
                            <label>Password</label>
                            <input type="password" v-model="mail_smtp_password"/>
                        </div>
                        <div class="formInput">
                            <label>From email</label>
                            <input type="text" v-model="mail_smtp_frommail"/>
                        </div>
                        <div class="formInput">
                            <label>From name</label>
                            <input type="text" v-model="mail_smtp_fromname"/>
                        </div>
                        <button class="button success" v-on:click="saveEmail">
                            Save
                        </button>
                        <button class="button" v-on:click="sendTestEmail">
                            Send a test email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

	import {get, set, sendTestmail} from 'js/Models/Setting';


	export default {

		data: () => {
			return {
				mail_smtp_host: '',
				mail_smtp_port: '',
				mail_smtp_user: '',
				mail_smtp_password: '',
				mail_smtp_frommail: '',
				mail_smtp_fromname: '',
			}
		},

		mounted: function () {
			get('mail_smtp_host').then((data) => {
				this.mail_smtp_host = typeof data.val !== 'undefined' ? data.val : ''
			});
			get('mail_smtp_port').then((data) => {
				this.mail_smtp_port = typeof data.val !== 'undefined' ? data.val : ''
			});
			get('mail_smtp_user').then((data) => {
				this.mail_smtp_user = typeof data.val !== 'undefined' ? data.val : ''
			});
			get('mail_smtp_password').then((data) => {
				this.mail_smtp_password = typeof data.val !== 'undefined' ? '**********' : ''
			});
			get('mail_smtp_frommail').then((data) => {
				this.mail_smtp_frommail = typeof data.val !== 'undefined' ? data.val : ''
			});
			get('mail_smtp_fromname').then((data) => {
				this.mail_smtp_fromname = typeof data.val !== 'undefined' ? data.val : ''
			});
		},
		methods: {
			saveEmail: async function () {
				await set('mail_smtp_host', this.mail_smtp_host);
				await set('mail_smtp_port', this.mail_smtp_port);
				await set('mail_smtp_user', this.mail_smtp_user);
				if (this.mail_smtp_password != '**********') {
					await set('mail_smtp_password', this.mail_smtp_password);
				}
				await set('mail_smtp_frommail', this.mail_smtp_frommail);
				await set('mail_smtp_fromname', this.mail_smtp_fromname);
			},
			sendTestEmail : async function() {
				try {
					let response = await sendTestmail();
					if (response.status == 'ok') {
						alert('A test email was sent to :' + response.sent_to);
					}
					else {
						alert('Error occured : '+response.message);
					}

				}
				catch(error) {
					alert('Error occured , please check log file or contract your administrator');
                }
            }
		}
	}

</script>