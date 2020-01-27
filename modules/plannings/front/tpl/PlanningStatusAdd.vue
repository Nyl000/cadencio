<template>
    <div>
        <div class="formInput">
            <label>Title</label>
            <input type="text" v-model="title"/>
        </div>
        <button v-bind:disabled="title === ''" class="button success" v-on:click="add">
            Add
        </button>
    </div>
</template>

<script>

    import {add} from 'js/Models/PlanningStatus';

    export default {
        props: ['onAdded'],

        data: () => {
            return {
                title: '',
            }
        },
        methods: {
            add : function() {
                add({
                    title: this.$data.title,
                }).then(() => {
                    this.$props.onAdded();
                })
            }
        },
    }

</script>