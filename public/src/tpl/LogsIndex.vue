<template>
    <div class="users_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-secondary" v-on:click="refreshGrid()">
                    <sync-icon/>
                </button>
            </div>
            <entity-table ref="table" name="tablemodules" :model="logsModel" :definition="tableDefinition"
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
	const logsModel = require('js/Models/Log');

	export default {
		data: function () {
			return {
				page: 1,
                logsModel: logsModel,
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
					saveurl: '/logs/{id}',
					title: this.$t('Logs'),
					columns: [
                        {
                            property: 'date_log', label: this.$t('Date'), sortable: true, renderer: {
                                type: Cell,
                            }
                        },
                        {
                            property: 'email', label: this.$t('User'), sortable: true, renderer: {
                                type: Cell,
                            }
                        },
						{
							property: 'type', label: this.$t('Type'), sortable: true, renderer: {
							type: Cell,
						    }
						},
                        {
                            property: 'comment', label: this.$t('Message'), sortable: true, renderer: {
                                type: Cell,
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