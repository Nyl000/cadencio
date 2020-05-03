<template>
    <div>
        <div class="list_wrapper">


            <entity-table v-if="ready" ref="table" :model="planningEntryModel" :listOptions="list_options" :definition="tableDefinition"
                          :page="this.$route.params.page || 1"/>

            <Modale ref="viewEntryDescriptionModal" class="modale-markdown">
                <h1 class="title">#{{selectedEntry.id}} - {{selectedEntry.title}}</h1>
                <hr/>
                <PlanningEntryDescription :entry="selectedEntry"/>
            </Modale>
        </div>
    </div>
</template>


<script>

    import {hasPermission, selectList as listUsers, getLoggedUser} from 'js/Models/User';
    import {deleteItem, toggleFollow} from 'js/Models/PlanningEntry';
    import {selectList as listStatuses, selectWithColor as listStatusesWithColor}  from 'js/Models/PlanningStatus';
    import PlanningEntryDescription from 'tpl/PlanningEntryDescription.vue';
    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableDateTime from 'tpl/Ui/EditableDateTime.vue';
    import EditableList from 'tpl/Ui/EditableList.vue';
    import TimeRemain from 'tpl/Ui/TimeRemain.vue';
    import Modale from 'tpl/Ui/Modale.vue';
    import DotsHorizontalIcon from 'vue-material-design-icons/DotsHorizontal.vue';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';
    import EntityTable from 'tpl/Ui/EntityTable';
    const planningEntryModel = require('js/Models/PlanningEntry');
    import EditableStatusList from 'tpl/Ui/EditableStatusList.vue';
    import TableCell from 'tpl/Ui/TableCell.vue';
    import ActionNotifyMe from 'tpl/Ui/ActionNotifyMe.vue';

    export default {
        props: ['id_planning', 'paginator_path', 'page', 'on_mounted', 'on_refresh'],

        data: function () {
            return {
                planningEntryModel: planningEntryModel,
                selectedEntry: {},
                statuses: [],
                statusesWithColor: [],
                users: [],
                tableDefinition: {},
                ready : false,
                list_options : {}
            }
        },
        mounted: function () {
            this.refreshTableDatas();
            if (typeof this.$props.on_mounted === 'function') {
                this.$props.on_mounted();
            }
        },

        methods: {
            hasPermission,

            setListOptions : function() {
                let filterStatus = localStorage.getItem('planningview_filterstatuses') ? JSON.parse(localStorage.getItem('planningview_filterstatuses')) : [];
                var listOptions = {
                    id_status: filterStatus
                };

                if (typeof this.$props.id_planning !== 'undefined') {
                    listOptions['id_planning'] = this.$props.id_planning;
                }
                this.list_options = listOptions;
            },

            refreshTableDatas: function () {
                let self = this;

                let tableDefinition = {
                    idField: 'id',
                    saveurl: '/planning_entry/{id}',
                    columns: [
                        {
                            property: 'id', label: 'ID', sortable: true, renderer: {
                            type: TableCell,
                        }
                        },
                        {
                            property: 'title', label: 'Title', sortable: true, renderer: {
                            type: EditableText,
                            placeholder: 'Title',
                            canUpdate: hasPermission('planning_entry', 'update'),
                        }
                        },
                        {
                            property: 'id_status', label: 'Status', sortable: true, renderer: {
                            type: EditableStatusList,
                            list: self.statuses,
                            placeholder: 'Status',
                            canUpdate: hasPermission('planning_entry', 'update'),
                        }
                        },
                        {
                            property: 'id_assigned_to', label: 'Assigned To', sortable: true, renderer: {
                            type: EditableList,
                            list: self.users,
                            placeholder: 'Not Assigned',
                            canUpdate: hasPermission('planning_entry', 'update'),
                        }
                        },
                        {
                            property: 'date_start', label: 'Start Date', sortable: true, renderer: {
                            type: EditableDateTime,
                            placeholder: 'Start Date',
                            canUpdate: hasPermission('planning_entry', 'update'),
                        }
                        },
                        {
                            property: 'date_end', label: 'Due Date', sortable: true, renderer: {
                            type: EditableDateTime,
                            placeholder: 'Due Date',
                            canUpdate: hasPermission('planning_entry', 'update'),
                            callback: this.refresh
                        }
                        },
                        {
                            property: 'date_end_due', label: 'Deadline', sortable: true, renderer: {
                            type: TimeRemain,
                            placeholder: 'Deadline',
                            canUpdate: false,
                        }
                        },
                    ],
                    actions: [
                        {
                            action: this.deleteEntryItem,
                            component: DeleteIcon,
                            canDisplay: hasPermission('planning_entry', 'delete'),
                            class: "icongrid"
                        },
                        {action: this.toggleFollow, component: ActionNotifyMe, canDisplay: true},
                        {
                            action: this.descriptionPlanningEntryModal,
                            component: DotsHorizontalIcon,
                            canDisplay: hasPermission('planning_entry', 'delete'),
                            class: "icongrid"
                        }
                    ]
                };
                this.tableDefinition = tableDefinition;
                this.ready = true;
            },
            refresh: function () {


                listStatuses().then((response) => {
                    this.statuses = response;
                    this.refreshTableDatas();
                    this.$forceUpdate();
                });
                listStatusesWithColor().then((response) => {
                    this.statusesWithColor = response;
                });

                listUsers().then((response) => {
                    this.users = response;
                    this.refreshTableDatas();
                });

                if (typeof this.$props.on_refresh === 'function') {
                    this.$props.on_refresh();
                }

                this.setListOptions();

            },
            descriptionPlanningEntryModal: function (entry) {
                this.selectedEntry = entry;
                this.$refs.viewEntryDescriptionModal.show();
            },
            deleteEntryItem: function (entry) {
                if (confirm('Confirm you want to delete this planning entry')) {
                    deleteItem(entry.id).then(() => {
                        this.refresh();
                    })
                }
            },
            toggleFollow(item) {
                toggleFollow(item.id, getLoggedUser().id).then(() => {
                    this.refresh();
                });
            },

        },
        components: {
            PlanningEntryDescription,
            Modale,
            DeleteIcon,
            DotsHorizontalIcon,
            EntityTable
        },


    }
</script>