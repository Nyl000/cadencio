<template>
    <div class="mainWrapper">
        <div v-if="isLogged" class="topBar">
            <div class="left">
                <div class="logowrap">
                    <div class="menuIcon" v-on:click="toggleMenu">
                        <menu-icon/>
                    </div>
                    <router-link to="/">
                        <img class="logo" src="/resources/images/logo.png" alt="Cadencio"/>
                    </router-link>
                </div>
            </div>
            <div class="right">
                <NotificationBox/>
                <router-link v-if="hasPermission('users','update_self')" to="/userprofile">
                    <account-icon/>
                </router-link>
                <div class="logout" v-on:click="logout">
                    <power-icon/>
                </div>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="appcontainer">
            <div v-if="isLogged" :class="'mainmenu ' + (menuOpen ? ' active' : '')">
                <div class="section" v-for="(section,name) in menuItems" v-if="section.canDisplay" :key="section.name">
                    <div class="title">
                        <span v-if="section.icon"><component :is="section.icon"/></span> {{section.title}}
                    </div>
                    <div v-for="(item,index) in section.entries">
                        <router-link v-if="item.canDisplay" :to="item.to"
                                     :key="index">
                            <span v-if="item.icon"><component :is="item.icon"/></span> {{item.title}}
                        </router-link>
                    </div>
                </div>
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
	import {getHooks} from 'js/Services/HookHandler';
	import AccountIcon from 'vue-material-design-icons/Account.vue';
	import PowerIcon from 'vue-material-design-icons/Power.vue';
	import MenuIcon from 'vue-material-design-icons/Menu.vue'
	import ToyBrickIcon from 'vue-material-design-icons/ToyBrick.vue'
	import CogOutlineIcon from 'vue-material-design-icons/SettingsOutline.vue'
	import AccountSupervisorCircleIcon from 'vue-material-design-icons/AccountSupervisorCircle.vue'
	import CrownIcon from 'vue-material-design-icons/Crown.vue'


	export default {

		data: () => {

			let menuHooks = getHooks('register_menu');
			let menuSectionsHooks = getHooks('register_menu_section');


			let menuItems = {
				'general': {
					name: 'cadencio_general',
					title: 'General',
					canDisplay: hasPermission('users', 'read') || hasPermission('roles', 'read'),
					entries: [
						{icon : AccountSupervisorCircleIcon, title: 'Users', to: '/users', canDisplay: hasPermission('users', 'read')},
						{icon : CrownIcon, title: 'Roles', to: '/roles', canDisplay: hasPermission('roles', 'read')}
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
				title: 'Settings',
				canDisplay: hasPermission('settings', '*'),
				entries: [
					{icon: ToyBrickIcon, title: 'Modules', to: '/modules', canDisplay: hasPermission('modules', '*')},
					{
						icon: CogOutlineIcon,
						title: 'App Settings',
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
				menuOpen: showMenu
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

				this.menuOpen = !this.menuOpen;
				localStorage.setItem('global_showmenu', this.menuOpen ? 1 : 0);

			}

		},
		components: {NotificationBox, AccountIcon, PowerIcon, MenuIcon}
	}
</script>
