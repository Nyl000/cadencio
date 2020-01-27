<template>
    <div class="planning_status_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addPlanningStatusModal"><plus-icon /> Add</button>
                <button class="button" v-on:click="refreshGrid()"><sync-icon/></button>
            </div>

            <table class="list">
                <tr class="items title">
                    <th class=" information" v-on:click="setOrder('title')">
                        Title
                        <menu-down-icon v-if="order == 'title' && orderDirection == 'ASC'" />
                        <menu-up-icon v-if="order == 'title' && orderDirection == 'DESC'" />
                    </th>
                    <th class="information">
                        Color
                    </th>
                    <th class="information">
                        Closed
                    </th>
                    <th>
                        Actions
                    </th>
                </tr>
                <tr class="items" v-for="status in planning_status" :key="status.id">
                    <td class="titleBlock information">
                        <EditableText v-bind:canupdate="hasPermission('planning_status','update')" v-bind:value="status.title"
                                      v-bind:saveurl="'/planning_status/'+status.id" field="title" placeholder="Title"/>
                    </td>
                    <td>
                        <EditableColor v-bind:canupdate="hasPermission('planning_status','update')" v-bind:value="status.color"
                                      v-bind:saveurl="'/planning_status/'+status.id" field="color" placeholder="Color"/>
                    </td>
                    <td>
                        <EditableCheckbox v-bind:canupdate="hasPermission('planning_status','update')" v-bind:value="status.closed"
                                       v-bind:saveurl="'/planning_status/'+status.id" field="closed" placeholder="Closed"/>
                    </td>
                    <td>
                        <delete-icon v-if="hasPermission('planning_status','delete')" class="delete" title="Delete status"
                           v-on:click="deleteItem(status.id)" />
                    </td>
                </tr>
            </table>
            <paginator :paginator="paginator" />
        </div>
        <Modale ref="addPlanningStatusModal">
            <PlanningStatusAdd v-bind:onAdded="onPlanningStatusAdded" />
        </Modale>
    </div>
</template>

<script>

    import {hasPermission} from 'js/Models/User';
    import {list,deleteItem}  from 'js/Models/PlanningStatus';

    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableColor from 'tpl/Ui/EditableColor.vue';
    import EditableCheckbox from 'tpl/Ui/EditableCheckbox.vue';

    import Paginator from 'tpl/Ui/Paginator.vue';

    import Modale from 'tpl/Ui/Modale.vue';
    import PlanningStatusAdd from 'tpl/PlanningStatusAdd.vue';

    import PlusIcon from 'vue-material-design-icons/Plus.vue';
    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue';
    import MenuUpIcon from 'vue-material-design-icons/MenuUp.vue';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';


    export default {
        data: () => {
            return {
                planning_status: [],
                paginator: {},
                order: 'title',
                orderDirection: 'ASC',
                page: 1
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

                let page = this.$route.params.page || 1;
                list({
                    order: this.order,
                    orderDirection: this.orderDirection,
                    page: page
                }).then((response) => {
                    this.planning_status = response.planning_status;
                    this.paginator = response.paginator;
                });

            },
            setOrder: function (field) {
                var orderDir = 'ASC';
                if (field === this.order) {
                    orderDir = this.orderDirection == 'ASC' ? 'DESC' : 'ASC';
                }
                this.order = field;
                this.orderDirection = orderDir;

                this.refreshGrid();
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
        EditableText,
        EditableColor,
        EditableCheckbox,
        Modale,
        PlanningStatusAdd,
        Paginator,
        PlusIcon,
        SyncIcon,
        MenuUpIcon,
        MenuDownIcon,
        DeleteIcon,
        }
    }

</script>