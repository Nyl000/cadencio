<template>
    <div>
        <table class="list">
            <tr class="items title">
                <th v-for="column in definition.columns" class=" information" v-on:click="setOrder(column.property)">
                    {{column.label}}
                    <menu-down-icon v-if="column.sortable && order == column.property && orderDirection == 'ASC'"/>
                    <menu-up-icon v-if="column.sortable && order == column.property && orderDirection == 'DESC'"/>
                </th>
                <th>
                    Actions
                </th>
            </tr>
            <tr class="items" v-for="item in list" :key="item.id">
                <td v-for="column in definition.columns" class="titleBlock information">
                        <component :is="column.renderer.type"
                                   :link="column.renderer.link ? column.renderer.link.replace('{id}',item.id) : false"
                                   :canupdate="column.renderer.canUpdate || false"
                                   :value="item[column.property]"
                                   :saveurl="definition.saveurl.replace('{id}',item.id)"
                                   :placeholder="column.renderer.placeholder || ''"
                                   :field="column.property"
                                   :list="column.renderer.list || {}"
                                   :style="column.renderer.style || {}"
                                   :class="column.renderer.cssClass || {}"
                                   :item="item"
                                   :callbackSuccess = "column.renderer.callback"
                        />
                </td>
                <td class="actions">
                    <ActionTable v-for="action in definition.actions"
                               v-if="action.canDisplay"
                               :item="item"
                                 :component="action.component"
                                 :action="action.action"
                                 :class="action.class"
                    />
                </td>
            </tr>
        </table>
        <paginator :paginator="paginator"/>
    </div>
</template>


<script>

    import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue';
    import MenuUpIcon from 'vue-material-design-icons/MenuUp.vue';
    import Paginator from 'tpl/Ui/Paginator.vue';
    import ActionTable from 'tpl/Ui/ActionTable.vue';

    export default {
        props: ['model', 'definition', 'page','listOptions'],

        mounted: function () {
            this.modelObj = this.model;
            this.refresh();

        },

        data: function () {
            return {
                order: '',
                orderDirection: 'ASC',
                list: [],
                paginator: {}
            }
        },

        methods: {
            setOrder: function (field) {
                var orderDir = 'ASC';
                if (field === this.order) {
                    orderDir = this.orderDirection == 'ASC' ? 'DESC' : 'ASC';
                }
                this.order = field;
                this.orderDirection = orderDir;

                this.refresh();

            },
            refresh: function () {

                let options ={
                    order: this.order,
                    orderDirection: this.orderDirection,
                    page: this.page,
                };
                options = Object.assign(options,this.$props.listOptions || {});


                this.modelObj.list(options).then((response) => {
                    this.list = response[Object.keys(response)[0]];
                    this.paginator = response.paginator;
                });

            },
        },
        components: {
            MenuUpIcon,
            MenuDownIcon,
            Paginator,
            ActionTable
        },
        watch: {
            page: function (newVal, oldVal) { // watch it
                this.refresh();
            },
            listOptions: function (newVal, oldVal) { // watch it
                this.refresh();
            },
        }
    }
</script>