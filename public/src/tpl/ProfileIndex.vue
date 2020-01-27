<template>
    <div class="profile_index">
        <div class="tablecontainer">
            <table class="list">
                <tr class="items title">
                    <th class=" information" v-on:click="setOrder('title')">
                        Option
                    </th>
                    <th class=" information" v-on:click="setOrder('title')">
                        Value
                    </th>
                </tr>
                <tr class="items">
                    <td class="titleBlock information">
                        Timezone
                    </td>
                    <td class="titleBlock information">
                        <select v-model="selectedTimezone" v-on:change="updateOption('timezone',selectedTimezone)">
                            <option v-for="(item,key) in timezones" v-bind:value="key">
                                {{item}}
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>

    import {hasPermission,updateUserOption,getUserOption} from 'js/Models/User';
    import {list, deleteItem}  from 'js/Models/Planning';

    import EditableText from 'tpl/Ui/EditableText.vue';
    import EditableList from 'tpl/Ui/EditableList.vue';
    import Paginator from 'tpl/Ui/Paginator.vue';

    import Modale from 'tpl/Ui/Modale.vue';
    import PlanningAdd from 'tpl/PlanningAdd.vue';

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
                selectedTimezone : getUserOption('timezone') || 'UTC',
            }
        },
        mounted: function () {

            this.refresh();

        },
        methods: {
            hasPermission: (resource, action) => {
                return hasPermission(resource, action);
            },
            updateOption : function(key,val ){
                updateUserOption(key,val);
            },
            refresh: function () {

            }

        },
        components: {EditableText,EditableList}
    }

</script>