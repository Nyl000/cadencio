<template>
    <div>
        <div class="list filters">
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
        </div>

        <div class="table-reponsive">
            <table class="list">
                <thead>
                <draggable v-model="columnsOrdered" @end="columnsOrderChange" tag="tr">
                    <th v-for="column in columnsOrdered" class=" information" :key="column.property"
                        v-on:click="setOrder(column.property)"
                        v-if="isDisplayed(column.property)">
                        {{column.label}}
                        <div class="sort">
                            <menu-down-icon
                                    v-if="column.sortable && order == column.property && orderDirection == 'ASC'"/>
                            <menu-up-icon
                                    v-if="column.sortable && order == column.property && orderDirection == 'DESC'"/>
                        </div>
                    </th>
                    <th v-if="typeof definition.actions !== 'undefined' && definition.actions.length > 0">
                        Actions
                    </th>
                </draggable>
                </thead>
                <tbody>
                <tr class="items" v-for="item in list" :key="item.id">
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
                        <ActionTable v-for="action in definition.actions"
                                     v-if="action.canDisplay"
                                     :item="item"
                                     :component="action.component"
                                     :action="action.action"
                                     :class="action.class"
                        />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <paginator :paginator="paginator"/>
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


	export default {
		props: ['model', 'definition', 'page', 'listOptions','name'],

		mounted: function () {
			this.modelObj = this.model;
			this.refresh();

		},

		data: function () {
			return {
				order: localStorage.getItem('table_' + this.$props.name + '_order') || '',
				orderDirection: localStorage.getItem('table_' + this.$props.name + '_orderDirection') || '',
				list: [],
				paginator: {},
				columnsDisplayed: [],
				columnsOrdered: [],
				chooserDisplayed: false,

			}
		},

		methods: {
			mounted: function () {
				this.setColumnsDisplayed();
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
				}
				else {
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
				let listTmp = this.list;
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
				}
				else {
					this.columnsDisplayed.push(id_property);
				}
				if (typeof  this.$props.name !== 'undefined') {
					localStorage.setItem('table_' + this.$props.name + '_columns', JSON.stringify(this.columnsDisplayed));
				}

			},

			refresh: function () {

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
			ActionTable
		},
		watch: {
			page: function (newVal, oldVal) { // watch it
				this.refresh();
			},
			listOptions: function (newVal, oldVal) { // watch it
				this.refresh();
			},
			definition: function () {
				this.setColumnsDisplayed();
				this.order = localStorage.getItem('table_' + this.$props.name + '_order') || '';
				this.orderDirection = localStorage.getItem('table_' + this.$props.name + '_orderDirection') || '';
			}
		}
	}
</script>