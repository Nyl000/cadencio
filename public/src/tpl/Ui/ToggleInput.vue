<template>
   <div @click="toggle" :class="'toggle ' + ( value == true ? 'on':'off')">
       <span class="slider"></span>
   </div>
</template>


<script>
	export default {
		props: ['value','canupdate'],
        methods : {
            toggle : function() {
                if(typeof this.canupdate !== 'undefined' && this.canupdate) {
                    let val = this.value == true;
                    this.$emit('input', !val);
                    this.$emit('change', !val);
                    this.$emit('click', !val);
                }
            }
        }
	}
</script>