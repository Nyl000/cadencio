<template>
    <div class="users_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addRoleModal">
                    <plus-icon/>
                    {{$t('Add')}}
                </button>
                <button class="button button-secondary" v-on:click="refreshGrid()">
                    <sync-icon/>
                </button>
            </div>
            <entity-table ref="table" name="tablerole" :model="rolesModel" :definition="tableDefinition"
                          :page="this.$route.params.page || 1"/>

        </div>
        <Modale ref="addRoleModal">
            <RoleAdd v-bind:onAdded="onRoleAdded"/>
        </Modale>
        <Modale ref="managePermissionsModal">
            <RoleManagePermissions :role="selectedRoleId"/>
        </Modale>
    </div>
</template>

<script>

	import {hasPermission} from 'js/Models/User';
	import {deleteItem} from 'js/Models/Role';
	import Modale from 'tpl/Ui/Modale.vue';
	import RoleAdd from 'tpl/RoleAdd.vue';
	import RoleManagePermissions from 'tpl/RoleManagePermissions.vue';
	import SyncIcon from 'vue-material-design-icons/Sync.vue';
	import PlusIcon from 'vue-material-design-icons/Plus.vue';
	import EntityTable from 'tpl/Ui/EntityTable';
	import EditableText from 'tpl/Ui/EditableText.vue';
	import DeleteIcon from 'vue-material-design-icons/Delete.vue';
	import GearIcon from 'vue-material-design-icons/Cog'
	const rolesModel = require('js/Models/Role');

	export default {
		data: function () {
			return {
				page: 1,
				rolesModel: rolesModel,
				selectedRoleId: null,
				tableDefinition: {},
			}
		},
		mounted: function () {

			this.refreshGrid();
			this.refreshTableDatas();

		},
		methods: {
			refreshTableDatas: function () {
				this.tableDefinition = {
					idField: 'id',
					saveurl: '/roles/{id}',
					title: this.$t('Roles'),
					columns: [
						{
							property: 'name', label: this.$t('Name'), sortable: true, renderer: {
							type: EditableText,
							placeholder: this.$t('Name'),
							canUpdate: hasPermission('roles', 'update'),
						}
						},
						{
							property: 'label', label: this.$t('Label'), sortable: true, renderer: {
							type: EditableText,
							placeholder: this.$t('Label'),
							canUpdate: hasPermission('roles', 'update'),
						}
						},
					],
					actions: [
						{
							action: this.managePermissionsModal,
							component: GearIcon,
							canDisplay: hasPermission('roles', 'update')
						},
						{action: this.deleteRole, component: DeleteIcon, canDisplay: hasPermission('roles', 'delete')},
					]
				}
			},
			hasPermission: (resource, action) => {
				return hasPermission(resource, action);
			},
			deleteRole: function (role) {
				if (confirm(this.$t('Please confirm you want to delete that role'))) {
					deleteItem(role.id).then(() => {
						this.refreshGrid();
					})
				}
			},
			refreshGrid: function () {
				this.$refs.table.refresh();
			},
			addRoleModal: function () {
				this.$refs.addRoleModal.show();
			},
			onRoleAdded: function () {
				this.$refs.addRoleModal.hide();
				this.refreshGrid();
			},
			managePermissionsModal: function (role) {
				this.selectedRoleId = role.id;
				this.$refs['managePermissionsModal'].show();
			},

		},

		components: {
			Modale,
			RoleAdd,
			RoleManagePermissions,
			SyncIcon,
			PlusIcon,
			EntityTable
		}
	}

</script>