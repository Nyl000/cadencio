<template>
    <transition name="fade">
        <span v-on:click="click" :title="title" >
            <component :is="component" v-bind="$props" :title="title" />
        </span>
    </transition>
</template>


<script>

    /*
     This is a helper function to inject buttons into an EntityTable
     */
	export default {
		props: ['component', 'action','item','title'],
		methods : {
			click : function() {
				this.$props.action(this.item);
			}
		}
	}
</script>