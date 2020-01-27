<template>
    <div class="markdown-entry-description">
        <vue-simple-markdown v-if="!editMode" :source="description"></vue-simple-markdown>
        <textarea v-if="editMode" v-model="description">
        </textarea>
        <div class="actionbar">
            <hr/>
            <div v-if="hasPermission('planning_entry','update')">
                <button v-if="!editMode" class="button" v-on:click="onPressEdit">Edit Description</button>
                <button v-if="editMode" class="button green" v-on:click="onPressSave">Save</button>

            </div>
        </div>
    </div>
</template>

<script>

    import {update} from 'js/Models/PlanningEntry';
    import {hasPermission} from 'js/Models/User';

    import moment from 'moment-timezone';

    export default {
        props: ['onAdded', 'idPlanning', 'entry','callbackSuccess'],

        data: function () {
            if (typeof this.$props.entry !== 'undefined') {
                return {
                    id: this.$props.entry.id,
                    description: this.$props.entry.description,
                    editMode: false,
                }
            }
        },
        methods: {
            hasPermission: hasPermission,
            onPressEdit: function () {
                this.editMode = true;
            },
            onCallback : function() {
                if (typeof this.$props.callbackSuccess !== 'undefined')  {
                    this.$props.callbackSuccess();
                }
            },
            onPressSave : function() {
                update(this.id, {description: this.description}).then(() => {
                    this.editMode = false;
                    this.onCallback();
                });
            }
        },
        components: {}
    }

</script>