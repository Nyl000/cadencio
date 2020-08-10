<script>

    import Rest from 'js/Services/Rest';

    export default {
        props: ['value', 'list', 'saveurl', 'id', 'field', 'canupdate','placeholder','callbackSuccess'],
        data: function () {
            return {
                editMode: false,
                val: this.value,
                success: false,
                error: false
            }
        },
        methods: {
            enterEditmode: function () {
                if (this.canupdate) {
                    this.editMode = true;
                    setTimeout(() => {
                        this.$refs.input.focus();
                    }, 10)
                }
            },
            onCallback : function() {
                if (typeof this.$props.callbackSuccess !== 'undefined')  {
                    this.$props.callbackSuccess(this.val);
                }
            },
            leaveEditmode: function () {
                this.editMode = false;
                this.error = false;
                let datas = {id: this.id};
                datas[this.field] = this.val;
                if (this.val !== this.value) {
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
            },
			leaveEditmodeNoCallback: function () {
				this.editMode = false
			}
        },
		watch: {
			value: function (newVal) {
				this.val = newVal;
			}
		}

    }

</script>