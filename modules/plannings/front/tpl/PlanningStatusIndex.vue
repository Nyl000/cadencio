<template>
    <div class="planning_status_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addPlanningStatusModal"><plus-icon /> Add</button>
                <button class="button" v-on:click="refreshGrid()"><sync-icon/></button>
            </div>

            <entity-table ref="table" :model="planningStatusModel" :definition="tableDefinition" :page="this.$route.params.page || 1" />

        </div>
        <Modale ref="addPlanningStatusModal">
            <PlanningStatusAdd v-bind:onAdded="onPlanningStatusAdded" />
        </Modale>
    </div>
</template>

<script>

    import {hasPermission} from 'js/Models/User';
    import {deleteItem}  from 'js/Models/PlanningStatus';
    import Modale from 'tpl/Ui/Modale.vue';
    import PlanningStatusAdd from 'tpl/PlanningStatusAdd.vue';
    import PlusIcon from 'vue-material-design-icons/Plus.vue';
    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import EntityTable from 'tpl/Ui/EntityTable';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';
    const planningStatusModel = require('js/Models/PlanningStatus');
    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableColor from 'tpl/Ui/EditableColor.vue';
    import EditableCheckbox from 'tpl/Ui/EditableCheckbox.vue';


    export default {
        data: function() {
            return {
                planningStatusModel:planningStatusModel,
                tableDefinition : {
                    idField: 'id',
                    saveurl:'/planning_status/{id}',
                    columns : [
                        {property: 'title', label : 'Title', sortable : true, renderer : {
                            type : EditableText,
                            placeholder: 'Title',
                            canUpdate : hasPermission('planning_status','update'),
                        }},
                        {property: 'color', label : 'Color', sortable : false, renderer : {
                            type : EditableColor,
                            placeholder: 'Color',
                            canUpdate : hasPermission('planning_status','update'),
                        }},
                        {property: 'closed', label : 'Closed', sortable : true, renderer : {
                            type : EditableCheckbox,
                            placeholder: 'Closed',
                            canUpdate : hasPermission('planning_status','update'),
                        }},
                    ],
                    actions : [
                        { action : this.deleteItem, component : DeleteIcon, canDisplay : hasPermission('planning_status','delete')  },
                    ]
                },
            }
        },
        mounted: function () {

            this.refreshGrid();

        },
        methods: {
            hasPermission: (resource, action) => {
                return hasPermission(resource, action);
            },
            deleteItem: function (userId) {
                if (confirm('Confirmez que vous voulez supprimer le status')) {
                    deleteItem(userId).then(() => {
                        this.refreshGrid();
                    })
                }
            },
            refreshGrid: function () {
                this.$refs.table.refresh();
            },

            addPlanningStatusModal: function () {
                this.$refs.addPlanningStatusModal.show();
            },
            onPlanningStatusAdded: function () {
                this.$refs.addPlanningStatusModal.hide();
                this.refreshGrid();
            }

        },
        watch: {
            '$route':function(newParam, oldParam) {
        if (newParam.params.page !== oldParam.params.page) {
            this.refreshGrid();
        }

    }
    },

    components: {
        Modale,
        PlanningStatusAdd,
        PlusIcon,
        SyncIcon,
        EntityTable
        }
    }

</script>