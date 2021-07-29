<template>
    <div class="page-container">
        <md-app style="min-height: 100vh;">
            <md-app-toolbar class=" app-bar"  md-elevation="3" v-if="isLogged">
                <md-button v-if="!menuVisible" class="md-icon-button" @click="menuVisible = !menuVisible">
                    <md-icon> <menu-icon/></md-icon>
                </md-button>
                <span class="md-title">
                    <router-link to="/">
                        <img class="md-image logo" :src="getLogo()" alt="logo"/>
                    </router-link>
                </span>
                <div class="md-toolbar-section-end header-end">
                        <NotificationBox/>
                        <router-link v-if="hasPermission('users','update_self')" to="/userprofile">
                            <md-icon><account-icon/></md-icon>
                        </router-link>
                        <div class="logout" v-on:click="logout">
                            <md-icon><power-icon/></md-icon>
                        </div>
                </div>
            </md-app-toolbar>

            <md-app-drawer class="main-menu-drawer" :md-active.sync="menuVisible" md-persistent="full" v-if="isLogged">
                <md-toolbar class="md-transparent menu-bar" md-elevation="3">
                      <span class="md-title">
                  Menu
                </span>
                    <div class="md-toolbar-section-end">
                        <md-button class="md-icon-button md-dense" @click="toggleMenu">
                            <md-icon><close-icon/></md-icon>
                        </md-button>
                    </div>
                </md-toolbar>

                <md-list class="list-menu-app" :md-expand-single="false">
                    <md-list-item md-expand :md-expanded="true" v-for="(section,name) in menuItems" v-if="section.canDisplay" :key="section.name">
                        <span class="md-list-item-text"> {{section.title}} </span>
                        <md-list slot="md-expand">
                            <md-list-item class="md-inset" v-for="(item,index) in section.entries" v-if="item.canDisplay" :to="item.to" :key="index">
                                    <md-icon class="md-icon-button md-dense"><component :is="item.icon"/></md-icon>
                                    <span class="md-list-item-text">{{item.title}}</span>
                            </md-list-item>
                        </md-list>
                    </md-list-item>

                </md-list>
            </md-app-drawer>

            <md-app-content class="md-layout md-size-100 md-alignment-top-center appzone">

                <router-view></router-view>

            </md-app-content>
        </md-app>
    </div>
</template>


<script>
	import {hasPermission} from 'js/Models/User';
	import NotificationBox from 'tpl/Ui/NotificationBox.vue';
	import {getHooks} from 'js/Services/HookHandler';
	import AccountIcon from 'vue-material-design-icons/Account.vue';
	import PowerIcon from 'vue-material-design-icons/Power.vue';
	import MenuIcon from 'vue-material-design-icons/Menu.vue'
	import ToyBrickIcon from 'vue-material-design-icons/ToyBrick.vue'
	import CogOutlineIcon from 'vue-material-design-icons/CogOutline.vue'
	import AccountSupervisorCircleIcon from 'vue-material-design-icons/AccountSupervisorCircle.vue'
	import CloseIcon from 'vue-material-design-icons/Close.vue'
    import MessageTextClockIcon from 'vue-material-design-icons/MessageTextClock.vue'
    import CrownIcon from 'vue-material-design-icons/Crown.vue'

	export default {

		data: function () {

			let menuHooks = getHooks('register_menu');
			let menuSectionsHooks = getHooks('register_menu_section');


			let menuItems = {
				'general': {
					name: 'cadencio_general',
					title:  this.$t('General'),
					canDisplay: hasPermission('users', 'read') || hasPermission('roles', 'read'),
					entries: [
						{icon : AccountSupervisorCircleIcon, title:  this.$t('Users'), to: '/users', canDisplay: hasPermission('users', 'read')},
						{icon : CrownIcon, title: this.$t('Roles'), to: '/roles', canDisplay: hasPermission('roles', 'read')},
                        {icon : MessageTextClockIcon, title: this.$t('Logs'), to: '/logs', canDisplay : hasPermission('logs','read')}
					]
				}

			};

			menuSectionsHooks.forEach((section) => {
				let s = section();
				s.entries = [];
				menuItems[s.name] = s;
			});


			//put settings at the end of menu
			menuItems.cadencio_settings = {
				name: 'cadencio_settings',
				title:  this.$t('Settings'),
				canDisplay: hasPermission('settings', '*'),
				entries: [
					{icon: ToyBrickIcon, title:  this.$t('Modules'), to: '/modules', canDisplay: hasPermission('modules', '*')},
					{
						icon: CogOutlineIcon,
						title:  this.$t('App Settings'),
						to: '/settings',
						canDisplay: hasPermission('settings', '*')
					}

				]
			};

			menuHooks.forEach((menu) => {
				let m = menu();
				menuItems[m.section].entries.push(m);
			});

			let showMenu = true;
			if (localStorage.getItem('global_showmenu') && localStorage.getItem('global_showmenu') == 0) {
				showMenu = false;
			}

			return {
				isLogged: localStorage.getItem('token') ? true : false,
				menuItems: menuItems,
                menuVisible: showMenu
			}
		},

		methods: {
			hasPermission,
			logout: () => {
				localStorage.removeItem('token');
				localStorage.removeItem('user');
				window.location.reload();
			},
			toggleMenu: function () {

				this.menuVisible = !this.menuVisible;
				localStorage.setItem('global_showmenu', this.menuOpen ? 1 : 0);

			},
            getLogo : function() {

                let logopath = '/resources/images/logo.png';
                let logoHooks = getHooks('override_menu_logo');

                logoHooks.forEach((hook) => {
                    logopath = hook(logopath);
                });

                return logopath;
            },

		},
		components: {NotificationBox, AccountIcon, PowerIcon, MenuIcon,CloseIcon}
	}
</script>
