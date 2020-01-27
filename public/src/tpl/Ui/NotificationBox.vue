<template>
    <div class="notif_wrap">
        <span class="icon_notifications" v-on:click="toggle">
            <bell-icon />
            <span class="number" v-if="notifications.length > 0">{{notifications.length}}</span>
        </span>
        <transition name="fade">
            <div v-if="visible" class="notifcation_box">
                <div class="overlay">
                    <div class="modalbox">
                        <div class="close" v-on:click="hide">
                            <close-icon />
                        </div>
                        <div class="content">
                            <div v-for="notification in notifications" class="notification">
                                <div class="date"><i class="fal fa-clock"></i> {{moment(dateToLocaleTime(notification.date)).local('fr').calendar()}}</div>
                                <div class="title">{{notification.title}}</div>
                            </div>
                            <div class="no_notif" v-if="notifications.length === 0">
                                No unseen notifications.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    import {getMyNotifications} from 'js/Models/User';
    import {dateToLocaleTime} from 'js/Services/Utils';
    import moment from 'moment-timezone';
    import Rest from 'js/Services/Rest';
    import BellIcon from 'vue-material-design-icons/Bell.vue';
    import CloseIcon from 'vue-material-design-icons/Close.vue';

    export default {
        data: () => {
            return {
                visible: false,
                notifications: [],
                interval: null,
            }
        },
        mounted: function () {
            this.refresh();
            this.interval = setInterval(this.refresh, 1000 * 60 * 5);
        },
        destroyed: function () {
            clearInterval(this.interval);
            this.interval = null;
        },
        methods: {
            dateToLocaleTime:dateToLocaleTime,
            moment:moment,
            show: function () {
                this.$data.visible = true;

            },

            hide: function () {
                this.$data.visible = false;
                this.notifications = [];
            },

            toggle: function () {
                this.$data.visible = !this.$data.visible;
                if (this.$data.visible) {
                    this.notifications.forEach((notification) => {
                        let notif = {id : notification.id};
                        notif.seen = 1;
                        Rest.authRequest('/notifications/'+notif.id, 'POST', notif);
                    })
                }
            },
            refresh: function () {
                getMyNotifications().then((datas) => {
                    this.notifications = datas.notifications;
                });
            }
        },
        components: {BellIcon,CloseIcon}
    }
</script>