<template>
    <div class="users_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <md-button class="md-raised md-primary" @click="addRoleModal">
                    <plus-icon/>
                    {{$t('Add')}}
                </md-button>
                <md-button class="md-secondary" @click="refreshGrid()">
                    <sync-icon/> {{$t('Refresh')}}
                </md-button>
            </div>
            <entity-table ref="table" name="tablerole" :model="rolesModel" :definition="tableDefinition"
                          :page="this.$route.params.page || 1"/>

        </div>
        <md-dialog :md-active.sync="showDialogAdd">
            <md-dialog-title>{{$t('Add role')}}</md-dialog-title>
            <RoleAdd v-bind:onAdded="onRoleAdded"/>
        </md-dialog>
        <md-dialog :md-active.sync="showDialogPermissions">
            <md-dialog-title>{{$t('Permissions for')}} {{selectedRoleName}}</md-dialog-title>
            <RoleManagePermissions :role="selectedRoleId"/>
        </md-dialog>
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
                selectedRoleName : null,
				tableDefinition: {},
                showDialogAdd : false,
                showDialogPermissions : false,
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
				this.showDialogAdd = true;
			},
			onRoleAdded: function () {
                this.showDialogAdd = false;
                this.refreshGrid();
			},
			managePermissionsModal: function (role) {
				this.selectedRoleId = role.id;
				this.selectedRoleName = role.label;
				this.showDialogPermissions = true;
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