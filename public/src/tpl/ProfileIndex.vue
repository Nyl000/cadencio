<template>
    <div class="profile_index">
        <div class="tablecontainer">
            <table class="list">
                <tr class="items title">
                    <th class=" information" v-on:click="setOrder('title')">
                        {{ $t('Option') }}
                    </th>
                    <th class=" information" v-on:click="setOrder('title')">
                        {{ $t('Value') }}
                    </th>
                </tr>
                <tr class="items">
                    <td class="titleBlock information">
                        {{ $t('Timezone') }}
                    </td>
                    <td class="titleBlock information">
                        <select v-model="selectedTimezone" v-on:change="updateOption('timezone',selectedTimezone)">
                            <option v-for="(item,key) in timezones" v-bind:value="key">
                                {{item}}
                            </option>
                        </select>
                    </td>
                </tr>
                <tr class="items">
                    <td class="titleBlock information">
                        {{ $t('lang') }}
                    </td>
                    <td class="titleBlock information">
                        <select v-model="selectedLang" v-on:change="updateOption('lang',selectedLang)">
                            <option v-for="(lang) in langs" v-bind:value="lang">
                                {{lang}}
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>

    import {hasPermission,updateUserOption} from 'js/Models/User';

    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableList from 'tpl/Ui/EditableList.vue';


    import {getSelect as selectTimezone} from 'js/Datas/Timezones';

    export default {
        data: () => {
            return {
                plannings: [],
                paginator: {},
                order: 'title',
                orderDirection: 'ASC',
                page: 1,
                timezones : selectTimezone(),
                selectedTimezone : 'UTC',
                langs: Config.langs || ['fr', 'en'],
                selectedLang:  'en',

            }
        },
        mounted: async function () {

            this.selectedTimezone = await this.$store.dispatch('login/getUserOptionAsync','timezone');
            this.selectedLang = await this.$store.dispatch('login/getUserOptionAsync','lang');

            this.$nextTick(() => {
                this.refresh();
            });

        },
        methods: {
            hasPermission: (resource, action) => {
                return hasPermission(resource, action);
            },
            updateOption : function(key,val ){
                updateUserOption(key,val);
                if(key == 'lang') {
                    this.$i18n.locale = val;
                }
            },
            refresh: function () {

            }

        },
        components: {EditableText,EditableList}
    }

</script>