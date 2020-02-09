<template>
    <ul class="paginator">
        <li v-if="parseInt(paginator.activePage,10)>1">
            <router-link v-bind:to="{name : path, params: {page:1}}">
                <chevron-left-icon /> First
            </router-link>
        </li>
        <li v-for="index in paginator.totalPages"
            v-show="index >= parseInt(paginator.activePage,10)-2 && index <= parseInt(paginator.activePage,10)+2 ">
            <router-link  v-bind:to="{name : path, params: {page:index}}" v-bind:class="{ active: index==paginator.activePage }">
                {{index}}
            </router-link>
        </li>
        <li v-if="parseInt(paginator.activePage,10)<parseInt(paginator.totalPages,10) ">
            <router-link  v-bind:to="{name : path, params: {page:paginator.totalPages}}">
                Last <chevron-right-icon />
            </router-link>
        </li>

    </ul>
</template>

<script>

    import ChevronLeftIcon from 'vue-material-design-icons/ChevronLeft.vue';
    import ChevronRightIcon from 'vue-material-design-icons/ChevronRight.vue';


    export default {
        props: ['paginator','path'],
        components: {ChevronLeftIcon,ChevronRightIcon}
    }
</script>