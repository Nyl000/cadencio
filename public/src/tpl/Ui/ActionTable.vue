<template>
    <transition name="fade">
        <div class="action_button">
            <md-button class="md-icon-button md-raised" v-on:click="click" :title="title" >
                <md-tooltip v-if="title" md-direction="top">{{title}}</md-tooltip>
                <component :is="component" v-bind="$props" title="" />
            </md-button>
        </div>


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