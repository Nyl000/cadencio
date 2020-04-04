<template>
    <transition name="fade">
        <span v-on:click="click" >
            <component :is="component" v-bind="$props"/>
        </span>
    </transition>
</template>


<script>

    /*
     This is a helper function to inject buttons into an EntityTable
     */
	export default {
		props: ['component', 'action','item'],
		methods : {
			click : function() {
				this.$props.action(this.item);
			}
		}
	}
</script>