<template>

    <div>
        <div class="simpleselect">
            <div class="title" v-if="title">{{title}}</div>
            <div class="hint" v-if="hint">
                {{hint}}
            </div>
            <div class="groupitems">
                <div>
                    <span class="item" v-if="typeof selectedItem[identifier] !== 'undefined' ">{{selectedItem[label]}}</span>
                    <em class="empty" v-if="typeof selectedItem[identifier] == 'undefined' ">{{placeholder}}</em>
                </div>
                <div class="button primary" @click.prevent="openSearch">{{ buttonText || "Select an item" }}</div>
            </div>
        </div>
        <Modale ref="searchModale">
            <SimpleSearch
                    :model="model"
                    :id_excluded="id_excluded"
                    :onSelected="onChange"
                    :selectedItem="selectedItem"
                    :placeholder="search_placeholder"
                    :identifier="identifier"
                    :label="label"
            />
        </Modale>
    </div>

</template>


<script>

	import SimpleSearch from 'tpl/Ui/SimpleSearch';
	import Modale from 'tpl/Ui/Modale';

	export default {
		props: [
			'selectedItem',
            'id_excluded',
            'model',
            'search_placeholder',
            'identifier',
            'label',
            'title',
            'hint',
            'onChangeCallback',
            'placeholder',
            'buttonText'

        ],

		mounted: async function () {


		},
		methods: {
			openSearch: function () {
				this.$refs.searchModale.show();
			},
			onChange: function (item) {
				this.$forceUpdate();
				this.$refs.searchModale.hide();

				if(typeof this.onChangeCallback === 'function') {
					this.onChangeCallback(item)
                }
			}

		},


		components: {
			Modale,
			SimpleSearch
		}
	}
</script>
