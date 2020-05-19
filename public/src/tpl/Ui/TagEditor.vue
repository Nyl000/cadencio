<template>
    <div class="tag_editor">
        <div class="title" v-if="title">{{title}}</div>
        <div class="hint" v-if="hint">
            {{hint}}
        </div>
        <div class="groupitems">
            <div v-show="!editMode" @click="enterEditMode">
                <span class="item" :key="tag" v-for="tag in tags">{{tag}}</span>
                <em class="empty" v-if="tags.length == 0">No tags</em>
            </div>
            <div v-show="editMode" class="formInput">
                <input type="text" ref="inputText" v-model="tagString" @blur="leaveEditMode"/>
            </div>
        </div>
    </div>
</template>


<script>


	export default {
		props: [
			'model',
			'title',
			'hint',
			'onChangeCallback',
            'tags'
		],
		data: () => {
			return {
				editMode: false,
                tagString : '',
			}
		},

		mounted: async function () {
            this.tagString = this.tags.join(', ');

		},
		methods: {

			onChange: function (items) {
				this.$forceUpdate();
				if(typeof this.onChangeCallback === 'function') {
					this.onChangeCallback(items)
				}
			},
            leaveEditMode : function() {
				let tags = this.tagString.split(',');
				for (let i=0;i<tags.length; i++) {
					tags[i] = tags[i].trim();
                }
				this.onChange(tags);
				this.editMode = false;
			},
            enterEditMode : function() {
				this.editMode = true;
				console.log(this.$refs);
				//Need to to this to let element appears before giving the focus.
				setTimeout(() => {
					this.$refs.inputText.focus();
                },20);
            }

		},
		watch : {
			tags : function(newval) {
				this.tagString = newval.join(', ');
			}
        }
	}
</script>
