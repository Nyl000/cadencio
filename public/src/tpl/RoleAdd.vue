<template>
    <div>
        <md-dialog-content>
            <md-field>
                <label for="name">{{$t('Name')}}</label>
                <md-input name="name" id="name" autocomplete="name" v-model="name"/>
            </md-field>
            <md-field>
                <label for="label">{{$t('Label')}}</label>
                <md-input name="label" id="label" autocomplete="label" v-model="label"/>
            </md-field>
        </md-dialog-content>
        <md-dialog-actions>
            <md-button v-bind:disabled="title === ''" class="md-primary md-raised" v-on:click="add">
                {{$t('Add')}}
            </md-button>
        </md-dialog-actions>
    </div>
</template>

<script>

    import {add} from 'js/Models/Role';

    export default {
        props: ['onAdded'],

        data: () => {
            return {
                name: '',
                label: '',
            }
        },
        methods: {
            add : function() {
                add({
                    name: this.$data.name,
                    label : this.$data.label,
                }).then(() => {
                    this.$props.onAdded();
                })
            }
        },
    }

</script>