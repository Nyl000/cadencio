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
                {{item[label]}}
            </li>
        </ul>
    </div>
</template>

<script>

	import MagnifyIcon from 'vue-material-design-icons/Magnify.vue';

	export default {
		props: ['onSelected','selectedItems','id_excluded', 'model','placeholder','identifier','label'],

		data: function()  {
			return {
				search: '',
				items: [],
				selected : this.$props.selectedItems ? this.$props.selectedItems : {},
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
				this.$props.onSelected(this.selected);
			}
		},
        components : { MagnifyIcon }
	}
</script>