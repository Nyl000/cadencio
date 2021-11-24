<template>
    <transition name="fade">
        <div class="editable">
            <div>
                <md-switch class="md-primary" :disabled="!canupdate" v-model="val" value="1" @change="leaveEditmode"/>
            </div>
        </div>
    </transition>
</template>

<script>
	import Rest from 'js/Services/Rest';
	import Editable from 'tpl/Ui/EditableMixin.vue';
	import CheckBoldIcon from 'vue-material-design-icons/CheckBold.vue';
	import CloseIcon from 'vue-material-design-icons/Close.vue';
	import ToggleInput from 'tpl/Ui/ToggleInput.vue';

	export default {
		mixins: [Editable],
		components: {CheckBoldIcon, CloseIcon, ToggleInput},
		methods: {
			leaveEditmode: function () {
				this.editMode = false;
				this.error = false;
				let datas = {id: this.id};
				datas[this.field] = this.val | 0;
				Rest.authRequest(this.saveurl, 'POST', datas).then(
					() => {
						this.success = true;
						setTimeout(() => {
							this.success = false;
							this.onCallback();
						}, 800);
					},
					() => {
						this.error = true;
					}
				);

			}
		}


	}
</script>