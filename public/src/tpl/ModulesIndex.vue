<template>
    <div class="users_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-secondary" v-on:click="refreshGrid()">
                    <sync-icon/>
                </button>
            </div>
            <entity-table ref="table" name="tablemodules" :model="modulesModel" :definition="tableDefinition"
                          :page="this.$route.params.page || 1"/>

        </div>
    </div>
</template>

<script>

	import {hasPermission} from 'js/Models/User';
	import SyncIcon from 'vue-material-design-icons/Sync.vue';
	import EntityTable from 'tpl/Ui/EntityTable';
	import Cell from 'tpl/Ui/Cell';
	import EditableCheckbox from 'tpl/Ui/EditableCheckbox';
	const modulesModel = require('js/Models/Module');

	export default {
		data: function () {
			return {
				page: 1,
				modulesModel: modulesModel,
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
					idField: 'name',
					saveurl: '/modules/{id}',
					title: this.$t('Modules'),
					columns: [
						{
							property: 'name', label: this.$t('Name'), sortable: true, renderer: {
							type: Cell,
						}
						},
						{
							property: 'version', label: this.$t('Version'), sortable: true, renderer: {
							type: Cell,
						}
						},
						{
							property: 'db_version', label: this.$t('Database Version'), sortable: false, renderer: {
							type: Cell,
						}
						},
						{
							property: 'active', label: this.$t('Active'), sortable: true, renderer: {
							type: EditableCheckbox,
							canUpdate: hasPermission('modules', 'update'),
                            callback : function() {
								modulesModel.refreshActivesModules(() => {
									window.location.reload(true);
                                })
                            }
						}
						},
					],
					actions: [
					]
				}
			},
			hasPermission: (resource, action) => {
				return hasPermission(resource, action);
			},
			refreshGrid: function () {
				this.$refs.table.refresh();
			},
		},

		components: {
			SyncIcon,
			EntityTable
		}
	}

</script>