<template>
    <div class="searchbox">
        <div v-if="!hide_search">
            <form v-on:submit.prevent="search">
                <input type="search" v-model="search" :placeholder="placeholder"/>
                <button class="button" v-on:click.prevent="searchItems"><magnify-icon /></button>
            </form>
        </div>
        <ul class="search_results">
            <li :class="' '+getActive(item[identifier]) " v-for="item in items" :key="item[identifier]" v-on:click="select(item[identifier],item)">
                {{item[label]}}
                <span class="closeitem" v-if="isSelected(item[identifier])" ><close-icon title="remove" /></span>
            </li>
        </ul>
        <div class="button submit" @click="onValidate">{{$t('Select')}}</div>
    </div>
</template>

<script>

	import MagnifyIcon from 'vue-material-design-icons/Magnify.vue';
    import CloseIcon from 'vue-material-design-icons/Close.vue';

	export default {
		props: ['onSelected','selectedItems','id_excluded', 'model','placeholder','identifier','label','hide_search','additionnal_filters','nb_items'],

		data: function()  {
			return {
				search: '',
				items:  [],
				selected : this.$props.selectedItems ? this.$props.selectedItems : {},
			}
		},

		mounted: function () {
			this.searchItems();
		},

		methods: {
			searchItems: function () {
			    let nbItems = this.nb_items || 99999;
                let options = { search : this.search,nbItems: nbItems};
                if (this.id_excluded) {
                    options.exclude  = this.id_excluded;
                }

                if (this.additionnal_filters && typeof this.additionnal_filters == 'object') {
                    options = {...options,...this.additionnal_filters};
                }

				this.model.list(options).then((results) => {
					this.items = results[Object.keys(results)[0]];
				});

			},
			isSelected : function(id) {
				return Object.keys(this.selected).indexOf(id) >= 0
			},
			getActive : function(id) {
				return this.isSelected(id) ? 'active' : '';
			},
			select: function (id,label) {

				if (this.isSelected(id)) {
					delete(this.selected[id]);
				}
				else {
					this.selected[id] = label;
				}
				this.$forceUpdate();
			},
            onValidate : function() {
                this.$props.onSelected(this.selected);
            }
		},
        components : { MagnifyIcon, CloseIcon }
	}
</script>