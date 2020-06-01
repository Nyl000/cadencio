<template>
    <div class="home">
        <default-home v-if="home_components.length == 0" />
        <div v-if="home_components.length > 0">
            <component v-for="c in home_components" :is="c" />
        </div>
    </div>
</template>

<script>

    import DefaultHome from 'tpl/DefaultHome.vue';
    import {getHooks} from 'js/Services/HookHandler';

    export default {

        data : function() {
            return {
                home_components : []
            }
        },

        components: {
            DefaultHome
        },

        mounted : function() {
            let components = getHooks('register_home_component');
            components.forEach((component) => {
                this.home_components.push(component());
            });

        }
    }

</script>