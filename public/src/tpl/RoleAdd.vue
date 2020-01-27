<template>
    <div>
        <div class="formInput">
            <label>Name</label>
            <input type="text" v-model="name"/>
        </div>
        <div class="formInput">
            <label>Label</label>
            <input type="text" v-model="label"  />
        </div>
        <button v-bind:disabled="title === ''" class="button success" v-on:click="add">
            Add
        </button>
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