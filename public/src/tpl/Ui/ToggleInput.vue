<template>
   <div @click="toggle" :class="'toggle ' + (value ? 'on':'off')">
       <span class="slider"></span>
   </div>
</template>


<script>
	export default {
		props: ['value'],
        methods : {
            toggle : function() {
            	this.$emit('input', !this.value);
            }
        }

	}
</script>