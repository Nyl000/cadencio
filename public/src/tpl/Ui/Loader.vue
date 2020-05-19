<template>
    <transition>
        <div class="loader">
            <div class="spin">
                <loading-icon/>
            </div>
        </div>
    </transition>

</template>

<script>
	import LoadingIcon from 'vue-material-design-icons/Loading.vue';
	export default {
		components: {LoadingIcon}
	}
</script>

