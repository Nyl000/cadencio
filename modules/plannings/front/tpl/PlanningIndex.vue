<template>
    <div class="planning_index">
        <div class="tablecontainer">
            <div class="actionbar">
                <button class="button button-add" v-on:click="addPlanningModal"><plus-icon /> Add</button>
                <button class="button" v-on:click="refreshGrid()"><sync-icon /></button>
            </div>

            <table class="list">
                <tr class="items title">
                    <th class=" information" v-on:click="setOrder('title')">
                        Title
                        <menu-down-icon v-if="order == 'title' && orderDirection == 'ASC'" />
                        <menu-up-icon v-if="order == 'title' && orderDirection == 'DESC'" />
                    </th>
                    <th>
                        Actions
                    </th>
                </tr>
                <tr class="items" v-for="planning in plannings" :key="planning.id">
                    <td class="titleBlock information">
                        <EditableText v-bind:link="'/planning/view/'+planning.id"
                                      v-bind:canupdate="hasPermission('plannings','update')"
                                      v-bind:value="planning.title"
                                      v-bind:saveurl="'/planning/'+planning.id" field="title" placeholder="Title"/>
                    </td>
                    <td>
                        <delete-icon v-if="hasPermission('planning','delete')" title="Delete planning"
                           v-on:click="deleteItem(planning.id)" />
                    </td>
                </tr>
            </table>
            <paginator :paginator="paginator"/>
        </div>
        <Modale ref="addPlanningModal">
            <PlanningAdd v-bind:onAdded="onPlanningAdded"/>
        </Modale>
    </div>
</template>

<script>

    import {hasPermission} from 'js/Models/User';
    import {list, deleteItem}  from 'js/Models/Planning';

    import EditableText from 'tpl/Ui/EditableText.vue';
    import Paginator from 'tpl/Ui/Paginator.vue';

    import Modale from 'tpl/Ui/Modale.vue';
    import PlanningAdd from 'tpl/PlanningAdd.vue';
    import PlusIcon from 'vue-material-design-icons/Plus.vue';
    import SyncIcon from 'vue-material-design-icons/Sync.vue';
    import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue';
    import MenuUpIcon from 'vue-material-design-icons/MenuUp.vue';
    import DeleteIcon from 'vue-material-design-icons/Delete.vue';

    export default {
        data: () => {
            return {
                plannings: [],
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
            deleteItem: function (id) {
                if (confirm('Confirmez que vous voulez supprimer le planning')) {
                    deleteItem(id).then(() => {
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
                    this.plannings = response.plannings;
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
            addPlanningModal: function () {
                this.$refs.addPlanningModal.show();
            },
            onPlanningAdded: function () {
                this.$refs.addPlanningModal.hide();
                this.refreshGrid();
            }

        },
        watch: {
            '$route': function (newParam, oldParam) {
                if (newParam.params.page !== oldParam.params.page) {
                    this.refreshGrid();
                }

            }
        },

        components: {EditableText, Modale, PlanningAdd, Paginator,PlusIcon,SyncIcon,MenuDownIcon,MenuUpIcon,DeleteIcon}
    }

</script>