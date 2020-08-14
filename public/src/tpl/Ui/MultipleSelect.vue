<template>

    <div>
        <div class="multipleselect">
            <div class="title" v-if="title">{{title}}</div>
            <div class="hint" v-if="hint">
                {{hint}}
            </div>
            <div class="groupitems">
                <div>
                    <span class="item" :key="item[identifier]" v-for="item in selectedItems">{{item[label]}}</span>
                    <em class="empty" v-if="Object.keys(selectedItems).length == 0">{{empty_label || 'No entry' }}</em>
                </div>
                <a href="#"  @click.prevent="openSearch">{{modify_button_label || 'Modify'}}</a>
            </div>
        </div>
        <Modale ref="searchModale">
            <MultipleSearch
                    :model="model"
                    :id_excluded="id_excluded"
                    :onSelected="onChange"
                    :selectedItems="selectedItems"
                    :placeholder="search_placeholder"
                    :identifier="identifier"
                    :label="label"
                    :hide_search="hide_search"
                    :additionnal_filters="additionnal_filters"
            />
        </Modale>
    </div>

</template>


<script>

	import MultipleSearch from 'tpl/Ui/MultipleSearch';
	import Modale from 'tpl/Ui/Modale';

	export default {
		props: [
			'selectedItems',
            'id_excluded',
            'model',
            'search_placeholder',
            'identifier',
            'label',
            'title',
            'hint',
            'onChangeCallback',
            'modify_button_label',
            'empty_label',
            'hide_search',
            'additionnal_filters'

        ],

		mounted: async function () {


		},
		methods: {
			openSearch: function () {
				this.$refs.searchModale.show();
			},
			onChange: function (items) {
				this.$forceUpdate();
				if(typeof this.onChangeCallback === 'function') {
					this.onChangeCallback(items)
                }
			}

		},


		components: {
			Modale,
			MultipleSearch
		}
	}
</script>
