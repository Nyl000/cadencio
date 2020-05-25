<template>
    <div class="searchbox">
        <div>
            <form v-on:submit.prevent="search">
                <input type="search" v-model="search" :placeholder="placeholder"/>
                <button class="button" v-on:click.prevent="searchItems"><magnify-icon /></button>
            </form>
        </div>
        <ul class="search_results">
            <li :class="' '+getActive(item[identifier]) " v-for="item in items" :key="item[identifier]" v-on:click="select(item[identifier],item)">
                {{renderItem(item)}}
            </li>
        </ul>
    </div>
</template>

<script>

	import MagnifyIcon from 'vue-material-design-icons/Magnify.vue';

	export default {
		props: ['onSelected','selectedItem','id_excluded', 'model','placeholder','identifier','label','itemRenderer'],

		data: function()  {
			return {
				search: '',
				items: [],
				selected : this.$props.selectedItem ? this.$props.selectedItem : {},
			}
		},
		mounted: function () {
			this.searchItems();
		},

		methods: {
			searchItems: function () {
                let options = { search : this.search,nbItems: 99999,};
                if (this.id_excluded) {
                	options.exclude = [this.id_excluded];
                }
				this.model.list(options).then((results) => {
					this.items = results[Object.keys(results)[0]];
				});
			},
			isSelected : function(id) {
				return this.selectedItem[this.identifier] == id
			},
			getActive : function(id) {
				return this.isSelected(id) ? 'active' : '';
			},
			renderItem : function(item) {
				if (typeof this.itemRenderer === 'function') {
					return this.itemRenderer(item);
                }
                else {
					return item[this.label];
                }
            },
			select: function (id,item) {

				if (this.isSelected(id)) {
					this.selected = {};
				}
				else {
					this.selected  = item;
				}
				this.$forceUpdate();
				this.$props.onSelected(this.selected);
			}
		},
        components : { MagnifyIcon }
	}
</script>