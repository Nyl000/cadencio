<template>
    <div class="entitytable">

        <div class="list filters">
            <a v-if="isExportable()" @click="exportItems" class="md-button-content exportbutton">
                EXPORT
                <export-icon/>
            </a>
            <div class="colselector">
                <format-columns-icon v-on:click="chooserDisplayed = !chooserDisplayed"/>
                <div class="chooser" v-if="chooserDisplayed">
                    <draggable v-model="columnsOrdered" @end="columnsOrderChange">

                        <div class="item" v-for="col in columnsOrdered" :key="col.property+'b'">
                            <div class="icon">
                                <checkbox-blank-circle-outline-icon
                                        v-on:click.prevent="toggleDisplay(col.property)"
                                        v-if="!isDisplayed(col.property)"/>
                                <checkbox-blank-circle-icon v-on:click.prevent="toggleDisplay(col.property)"
                                                            v-if="isDisplayed(col.property)"/>
                            </div>
                            {{col.label}}
                        </div>
                    </draggable>
                </div>
            </div>
            <div class="tabletitle">{{definition.title}}</div>
            <div class="counters" v-if="!load">
                <md-chip v-if="paginator.totalItems > 0">{{paginator.totalItems}} {{ paginator.totalItems > 1 ? $t('lines') : $t('line') }}</md-chip>
                <md-chip :key="key" v-if="val" v-for="(val,key) in paginator.totals">{{getTotalsLabel(key)}}: {{getTotalRenderer(key,val)}} {{getTotalsUnit(key)}}</md-chip>

            </div>
        </div>

        <div class="table-responsive">
            <table :class="'list ' + (isSelectable() ? 'selectable' : '')">
                <thead ref="tablehead">
                <draggable v-model="columnsOrdered" @end="columnsOrderChange" tag="tr">
                    <th v-for="column in columnsOrdered" class=" information" :key="column.property"
                        v-on:click="column.sortable ?  setOrder(typeof column.order_field !== 'undefined' ? column.order_field  : column.property) : ()=>{}"
                        v-if="isDisplayed(column.property)">
                        {{column.label}}
                        <div class="sort">
                            <menu-down-icon
                                    v-if="getOrderStatus(order,orderDirection,column) == 'ASC'"/>
                            <menu-up-icon
                                    v-if="getOrderStatus(order,orderDirection,column) == 'DESC'"/>
                        </div>
                    </th>
                    <th v-if="typeof definition.actions !== 'undefined' && definition.actions.length > 0">
                        Actions
                    </th>
                </draggable>
                </thead>
                <tbody>
                <tr :class="'items ' + (isSelected(item) ? 'selected' : '') " v-for="item in list" :key="item.id" @click="doSelect(item)">
                    <td v-for="(column,index) in columnsOrdered" class="titleBlock information"
                        v-if="isDisplayed(column.property)"
                        :style="{minWidth : column.renderer.minWidth || 'auto'}">
                        <span class="titleresponsive">{{column.label}}</span>
                        <component
                                :is="column.renderer.type"
                                :link="column.renderer.link ? column.renderer.link.replace('{id}',item[definition.idField]) : false"
                                :canupdate="column.renderer.canUpdate || false"
                                :value="item[column.property]"
                                :saveurl="definition.saveurl.replace('{id}',item[definition.idField])"
                                :placeholder="column.renderer.placeholder || ''"
                                :field="column.property"
                                :list="column.renderer.list || {}"
                                :style="column.renderer.style || {}"
                                :class="column.renderer.cssClass || {}"
                                :item="item"
                                :callbackSuccess="column.renderer.callback"
                                :onclick="column.renderer.onclick || function(){}"
                        />
                    </td>
                    <td class="actions"
                        v-if="typeof definition.actions !== 'undefined' && definition.actions.length > 0">
                        <div class="titleresponsive">Actions</div>
                        <ActionTable v-for="(action,index) in definition.actions"
                                     v-if="action.canDisplay && (typeof action.canDisplayInRow !== 'undefined' ? action.canDisplayInRow(item) : true)"
                                     :item="item"
                                     :component="action.component"
                                     :action="action.action"
                                     :class="action.class"
                                     :key="index"
                                     :title="action.title"
                        />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <paginator :paginator="paginator"/>
        <Loader v-if="load"/>

    </div>
</template>


<script>

    import MenuDownIcon from 'vue-material-design-icons/MenuDown.vue';
    import MenuUpIcon from 'vue-material-design-icons/MenuUp.vue';
    import FormatColumnsIcon from 'vue-material-design-icons/FormatColumns.vue';
    import CheckboxBlankCircleOutlineIcon from 'vue-material-design-icons/CheckboxBlankCircleOutline.vue';
    import CheckboxBlankCircleIcon from 'vue-material-design-icons/CheckboxBlankCircle.vue';
    import Paginator from 'tpl/Ui/Paginator.vue';
    import ActionTable from 'tpl/Ui/ActionTable.vue';
    import draggable from 'vuedraggable'
    import Loader from 'tpl/Ui/Loader.vue';
    import ExportIcon from 'vue-material-design-icons/Export.vue';
    import {hasPermission, getTempToken,getUserOptionAsync,resyncUserDatas} from 'js/Models/User';

    export default {
        props: ['model', 'definition', 'page', 'listOptions', 'name', 'loadOnStart', 'resource','selectable','entityName', 'totals_labels', 'totals_units','totals_renderers'],

        data: function () {
            return {
                order : '',
                orderDirection : '',
                list: [],
                selected: {},
                paginator: {},
                columnsDisplayed: [],
                columnsOrdered: [],
                chooserDisplayed: false,
                load: false,
                loadStart: typeof this.$props.loadOnStart !== 'undefined' ? this.$props.loadOnStart : true,
                entity_name : this.$props.entityName,
            }
        },

        mounted : function() {
            this.modelObj = this.model;
            this.resyncUserOptions().then(() => {
                if (this.loadStart) {
                    this.$nextTick(() => {
                        this.refresh();
                    });
                }
            })
        },



        created() {
            window.addEventListener('scroll', this.onScroll);
        },
        destroyed() {
            window.removeEventListener('scroll', this.onScroll);
        },


        methods: {
            resyncUserOptions : async function() {
                if (this.entity_name) {

                    this.order = await getUserOptionAsync(this.entity_name + '_order');
                    this.orderDirection = await  getUserOptionAsync(this.entity_name +'_orderDirection');

                }
                else {
                    this.order = localStorage.getItem('table_' + this.$props.name + '_order');
                    this.orderDirection = localStorage.getItem('table_' + this.$props.name + '_orderDirection');
                }

            },
            getTotalsLabel(label) {
                if (typeof this.totals_labels == 'object' && typeof this.totals_labels[label] !== 'undefined' ) {
                    return this.totals_labels[label];
                }
                return label;
            },
            getTotalsUnit(label) {
                if (typeof this.totals_units == 'object' && typeof this.totals_units[label] !== 'undefined' ) {
                    return this.totals_units[label];
                }
                return '';
            },

            getTotalRenderer(label,value) {
                if (typeof this.totals_renderers == 'object' && typeof this.totals_renderers[label] == 'function' ) {
                    return this.totals_renderers[label](value);
                }
                return value;
            },
            onScroll: function () {
                this.$refs.tablehead.style.position = 'relative';
                this.$refs.tablehead.style.transform = 'translateY(0px) translateZ(10px)';

                let topOffset = this.$refs.tablehead.getBoundingClientRect().top
                if (topOffset < 0) {
                    let height = Math.abs(topOffset - this.$refs.tablehead.clientHeight);
                    this.$refs.tablehead.style.transform = 'translateY(' + Math.abs(topOffset) + 'px) translateZ(10px)';

                }
            },
            isSelectable : function() {
                return typeof this.selectable !== 'undefined' && this.selectable;
            },
            isSelected : function(item) {
                return item[this.definition.idField] == this.selected[this.definition.idField];
            },
            doSelect : function(item) {
                if (this.isSelectable()) {
                    this.selected = item;
                    this.$emit('select', item);
                }
            },
            isExportable: function () {
                return hasPermission(this.resource, 'export') && typeof (this.modelObj) !== 'undefined' && typeof (this.modelObj.getExportUrl) !== 'undefined';
            },
            exportItems: async function () {
                let options = {
                    order: this.order,
                    orderDirection: this.orderDirection,
                    page: this.page,
                };
                options = Object.assign(options, this.$props.listOptions || {});

                let dataToken = await getTempToken();

                options.authtoken = dataToken.token;
                window.open(this.modelObj.getExportUrl(options));

            },
            getOrderStatus: function (currorder, curdirection, column) {
                let field = typeof column.order_field !== 'undefined' ? column.order_field : column.property
                if (column.sortable && currorder == field && curdirection == 'ASC') {
                    return 'ASC';
                }
                if (column.sortable && currorder == field && curdirection == 'DESC') {
                    return 'DESC';
                }
                return 'NONE';

            },
            setOrder: function (field) {
                var orderDir = 'ASC';
                if (field === this.order) {
                    orderDir = this.orderDirection == 'ASC' ? 'DESC' : 'ASC';
                }
                this.order = field;
                this.orderDirection = orderDir;
                if (typeof this.$props.name !== 'undefined') {
                    localStorage.setItem('table_' + this.$props.name + '_order', field);
                    localStorage.setItem('table_' + this.$props.name + '_orderDirection', orderDir);
                }

                this.refresh();

            },
            setColumnsDisplayed: function () {

                let localCol = localStorage.getItem('table_' + this.$props.name + '_columns');
                if (localCol) {
                    this.columnsDisplayed = JSON.parse(localCol);
                } else {
                    this.columnsDisplayed = [];
                    this.$props.definition.columns.forEach((col) => {
                        this.columnsDisplayed.push(col.property);
                    });
                }

                if (typeof this.$props.definition.columns !== 'undefined') {
                    this.getColumnsOrder();
                }


            },
            columnsOrderChange: function () {
                localStorage.setItem('table_' + this.$props.name + '_orderedColumns', JSON.stringify(this.columnsOrdered));
                this.refresh();

            },
            getColumnsOrder: function () {

                this.columnsOrdered = JSON.parse(localStorage.getItem('table_' + this.$props.name + '_orderedColumns')) || [];

                //Add new columns
                this.definition.columns.forEach((realCol, indexDef) => {
                    let found = false;
                    this.columnsOrdered.forEach((localCol, indexLocal) => {
                        if (realCol.property == localCol.property) {
                            found = true;
                            this.columnsOrdered[indexLocal] = this.definition.columns[indexDef];

                        }
                    });
                    if (!found) {
                        this.columnsOrdered.push(realCol);
                    }
                });
                //Remove old columns
                this.columnsOrdered.forEach((localCol) => {
                    let found = false;
                    this.definition.columns.forEach((realCol) => {
                        if (realCol.property == localCol.property) {
                            found = true;
                        }
                    });
                    if (!found) {
                        this.columnsOrdered.splice(this.columnsOrdered.indexOf(realCol), 1);
                    }
                });


            },

            isDisplayed: function (id_property) {
                return this.columnsDisplayed.indexOf(id_property) >= 0;
            },

            toggleDisplay: function (id_property) {

                let index = this.columnsDisplayed.indexOf(id_property);
                if (index >= 0) {
                    this.columnsDisplayed.splice(index, 1);
                } else {
                    this.columnsDisplayed.push(id_property);
                }
                if (typeof this.$props.name !== 'undefined') {
                    localStorage.setItem('table_' + this.$props.name + '_columns', JSON.stringify(this.columnsDisplayed));
                }

            },

            refresh: function () {

                this.load = true;
                let options = {
                    order: this.order,
                    orderDirection: this.orderDirection,
                    page: this.page,
                };
                options = Object.assign(options, this.$props.listOptions || {});

                this.list = [];

                this.modelObj.list(options).then((response) => {
                    this.list = response[Object.keys(response)[0]];
                    this.paginator = response.paginator;
                    this.load = false;
                    this.$emit('refresh',this.list);
                });
            },
        },
        components: {
            MenuUpIcon,
            MenuDownIcon,
            FormatColumnsIcon,
            CheckboxBlankCircleOutlineIcon,
            CheckboxBlankCircleIcon,
            Paginator,
            draggable,
            ActionTable,
            Loader,
            ExportIcon
        },
        watch: {
            page: function (newVal, oldVal) { // watch it
                this.refresh();
            },
            listOptions: function (newVal, oldVal) { // watch it
                if (JSON.stringify(newVal) != JSON.stringify(oldVal)) {
                    this.refresh();
                }
            },
            definition: function () {
                this.setColumnsDisplayed();
                this.resyncUserOptions();
            }
        }
    }
</script>