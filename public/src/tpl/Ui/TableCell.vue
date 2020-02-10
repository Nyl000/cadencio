<template>
    <transition name="fade">
       <div class="tableCell">
           {{value}}
       </div>
    </transition>
</template>


<script>

    /*
    This is a helper function to inject simple content tinto a EntityTable
     */
    export default {
        props: ['value'],
    }
</script>