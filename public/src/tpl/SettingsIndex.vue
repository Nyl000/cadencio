<template>
    <div class="settingsindex md-layout md-alignment-top-left">
        <div class="md-layout-item md-size-33 md-large-size-33 md-small-size-80 md-xsmall-size-100">

            <md-card>
                <md-card-header>
                    <h2>{{$t('Email Settings')}}</h2>
                </md-card-header>
                <md-card-content>
                    <h3>{{$t('SMTP')}}</h3>
                    <md-field>
                        <label for="mail_smtp_host">{{$t('Host')}}</label>
                        <md-input name="mail_smtp_host" id="mail_smtp_host" autocomplete="mail_smtp_host" v-model="mail_smtp_host"/>
                    </md-field>
                    <md-field>
                        <label for="mail_smtp_port">{{$t('Port')}}</label>
                        <md-input name="mail_smtp_port" id="mail_smtp_port" autocomplete="mail_smtp_port" v-model="mail_smtp_port"/>
                    </md-field>
                    <md-field>
                        <label for="mail_smtp_user">{{$t('Username')}}</label>
                        <md-input  name="mail_smtp_user" id="mail_smtp_user" autocomplete="mail_smtp_user" v-model="mail_smtp_user"/>
                    </md-field>
                    <md-field>
                        <label for="mail_smtp_password">{{$t('Password')}}</label>
                        <md-input type="password" name="mail_smtp_password" id="mail_smtp_password" autocomplete="mail_smtp_password" v-model="mail_smtp_password"/>
                    </md-field>
                    <h3>{{$t('Identity')}}</h3>
                    <md-field>
                        <label for="mail_smtp_frommail">{{$t('From Email')}}</label>
                        <md-input type="email"  name="mail_smtp_frommail" id="mail_smtp_frommail" autocomplete="mail_smtp_password" v-model="mail_smtp_frommail"/>
                    </md-field>
                    <md-field>
                        <label for="mail_smtp_frommail">{{$t('From Name')}}</label>
                        <md-input  name="mail_smtp_fromname" id="mail_smtp_fromname" autocomplete="mail_smtp_fromname" v-model="mail_smtp_fromname"/>
                    </md-field>
                </md-card-content>
                <md-card-actions>
                    <md-button class="md-primary md-raised" v-on:click="saveEmail">
                        {{$t('Save')}}
                    </md-button>
                    <md-button class="secondary " v-on:click="sendTestEmail">
                        {{$t('Send me a test email')}}
                    </md-button>
                </md-card-actions>
            </md-card>
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
            sendTestEmail: async function () {
                try {
                    let response = await sendTestmail();
                    if (response.status == 'ok') {
                        alert('A test email was sent to :' + response.sent_to);
                    } else {
                        alert('Error occured : ' + response.message);
                    }

                } catch (error) {
                    alert('Error occured , please check log file or contract your administrator');
                }
            }
        }
    }

</script>