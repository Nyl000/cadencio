<template>
    <div class="sticky_wrap">
        <div class="sticky_elem" ref="sticky">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        created () {
            window.addEventListener('scroll', this.onScroll);
        },
        destroyed () {
            window.removeEventListener('scroll', this.onScroll);
        },
        methods : {
            onScroll : function() {
                this.$refs.sticky.style.position = 'relative';
                this.$refs.sticky.style.transform = 'translateY(0px) translateZ(10px)';

                let topOffset = this.$refs.sticky.getBoundingClientRect().top;

                if (topOffset < 0) {
                    this.$refs.sticky.style.transform = 'translateY('+Math.abs(topOffset)+'px) translateZ(10px)';
                }
            },
        }
    }
</script>