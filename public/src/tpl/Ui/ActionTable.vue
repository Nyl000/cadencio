<template>
    <transition name="fade">
        <md-button class="md-icon-button md-raised" v-on:click="click" :title="title" >
            <component :is="component" v-bind="$props" :title="title" />
        </md-button>
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