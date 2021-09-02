<template>
    <div class="cell cell-image">
        <img :src="value" alt="" />
    </div>
</template>


<script>
	export default {
		props: ['component', 'item', 'value'],
	}
</script>