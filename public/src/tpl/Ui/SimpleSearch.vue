<template>
    <div class="searchbox">
        <div>
            <form v-on:submit.prevent="search">
                <input type="search" v-model="search" :placeholder="placeholder"/>
                <button class="button" v-on:click.prevent="searchItems">
                    <magnify-icon/>
                </button>
            </form>
        </div>
        <div class="resultzone">
            <small-loader v-if="isLoading"/>
            <ul class="search_results">
                <li :class="' '+getActive(item[identifier]) " v-for="item in items" :key="item[identifier]"
                    v-on:click="select(item[identifier],item)">
                    {{renderItem(item)}}
                </li>
            </ul>
        </div>

    </div>
</template>

<script>

    import MagnifyIcon from 'vue-material-design-icons/Magnify.vue';
    import SmallLoader from 'tpl/Ui/Loader';

    export default {
        props: ['onSelected', 'selectedItem', 'id_excluded', 'model', 'placeholder', 'identifier', 'label', 'itemRenderer', 'additionalsFilters'],

        data: function () {
            return {
                isLoading: true,
                search: '',
                items: [],
                selected: this.$props.selectedItem ? this.$props.selectedItem : {},
            }
        },
        mounted: function () {
            this.searchItems();
        },

        methods: {
            searchItems: async function () {
                this.isLoading = true;
                let additionalsFilters = this.additionalsFilters || {};
                let options = {...additionalsFilters, search: this.search, nbItems: 99999,};
                if (this.id_excluded) {
                    options.exclude = [this.id_excluded];
                }
                let res = await this.model.list(options);
                this.items = res[Object.keys(res)[0]];

                this.isLoading = false;
            },
            isSelected: function (id) {
                return this.selectedItem[this.identifier] == id
            },
            getActive: function (id) {
                return this.isSelected(id) ? 'active' : '';
            },
            renderItem: function (item) {
                if (typeof this.itemRenderer === 'function') {
                    return this.itemRenderer(item);
                } else {
                    return item[this.label];
                }
            },
            select: function (id, item) {

                if (this.isSelected(id)) {
                    this.selected = {};
                } else {
                    this.selected = item;
                }
                this.$forceUpdate();
                this.$props.onSelected(this.selected);
            }
        },
        components: {MagnifyIcon, SmallLoader}
    }
</script>