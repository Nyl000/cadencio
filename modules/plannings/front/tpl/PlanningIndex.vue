<template>
    <div class="planning_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addPlanningModal"><plus-icon /> Add</button>
                <button class="button button-secondary" v-on:click="refreshGrid()"><sync-icon /></button>
            </div>
            <entity-table name="planningstable" ref="table" :model="planningModel" :definition="tableDefinition" :page="this.$route.params.page || 1" />
        </div>

        <Modale ref="addPlanningModal">
            <PlanningAdd v-bind:onAdded="onPlanningAdded"/>
        </Modale>
    </div>
</template>

<script>

    import {hasPermission} from 'js/Models/User';
    import Modale from 'tpl/Ui/Modale.vue';
    import PlanningAdd from 'tpl/PlanningAdd.vue';
    import PlusIcon from 'vue-material-design-icons/Plus.vue';
    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import EntityTable from 'tpl/Ui/EntityTable';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';
    import EditableText from 'tpl/Ui/EditableText.vue';

    const planningModel = require('js/Models/Planning');

    export default {
        data: function () {
            return {
                page:  1,
                planningModel:planningModel,
                tableDefinition : {},
            }
        },
        mounted: function () {

            this.refreshGrid();
            this.refreshTableDatas();

        },
        methods: {
        	refreshTableDatas : function() {
        		this.tableDefinition = {
					idField: 'id',
					saveurl:'/planning/{id}',
					title: 'Plannings',
					columns : [
						{property: 'title', label : 'Title', sortable : true, renderer : {
							type : EditableText,
							placeholder: 'Title',
							canUpdate : hasPermission('planning','update'),
							link : '/planning/view/{id}',
						}},
					],
					actions : [
						{ action : this.deleteItem, component : DeleteIcon, canDisplay : hasPermission('planning','delete')  },
					]
                }
            },
            hasPermission: (resource, action) => {
                return hasPermission(resource, action);
            },
            deleteItem: function (id) {
                if (confirm('Confirmez que vous voulez supprimer le planning')) {
                    deleteItem(id).then(() => {
                        this.refreshGrid();
                    })
                }
            },
            refreshGrid: function () {
               this.$refs.table.refresh();
            },
            addPlanningModal: function () {
                this.$refs.addPlanningModal.show();
            },
            onPlanningAdded: function () {
                this.$refs.addPlanningModal.hide();
                this.refreshGrid();
            }

        },

        components: {Modale, PlanningAdd,PlusIcon,SyncIcon,EntityTable}
    }

</script>