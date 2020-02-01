<template>
    <div class="mainWrapper">
        <div v-if="isLogged" class="topBar">
            <div class="left">
                <div class="logowrap">
                    <router-link to="/">
                        <img class="logo" src="/resources/images/logo.png" alt="Cadencio"/>
                    </router-link>
                </div>
            </div>
            <div class="right">
                <NotificationBox/>
                <router-link v-if="hasPermission('users','update_self')" to="/userprofile">
                    <account-icon />
                </router-link>
                <div class="logout" v-on:click="logout">
                    <power-icon />
                </div>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="appcontainer">
            <div v-if="isLogged" class="mainmenu">
                <router-link v-for="(item,index) in menuItems"  v-if="item.canDisplay" :to="item.to" :key="index">
                    {{item.title}}
                </router-link>
            </div>
            <div class="appview">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>

<script>
    import {hasPermission} from 'js/Models/User';
    import NotificationBox from 'tpl/Ui/NotificationBox.vue';
    import {getEvents} from 'js/Services/EventHandler';
    import AccountIcon from 'vue-material-design-icons/Account.vue';
    import PowerIcon from 'vue-material-design-icons/Power.vue';

    export default {

        data: () => {

            let menuEvents = getEvents('register_menu');
            let menuItems = [
                {  title : 'Users', to : '/users', canDisplay: hasPermission('users','read')},
                {  title : 'Roles', to : '/roles', canDisplay: hasPermission('roles','read')}
            ];
            menuEvents.forEach((menu) => {
                menuItems.push(menu());
            });

            return {
                isLogged: localStorage.getItem('token') ? true : false,
                menuItems : menuItems
            }
        },

        methods: {
            hasPermission,
            logout: () => {
                localStorage.removeItem('token');
                localStorage.removeItem('user');

                window.location.reload();

            },

        },
        components: {NotificationBox,AccountIcon,PowerIcon}
    }
</script>
