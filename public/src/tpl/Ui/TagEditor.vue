<template>
    <div class="tag_editor">
        <div class="title" v-if="title">{{title}}</div>
        <div class="hint" v-if="hint">
            {{hint}}
        </div>
        <div class="groupitems">
            <div class="tags">
                <span class="item" :key="tag" v-for="tag in tags">{{tag}} <close-icon class="delete" @click="remove(tag)"/> </span>
                <em class="empty" v-if="tags.length == 0">{{noTagsLabel || 'No Tags'}}</em>
            </div>
            <div class="formInput">
                <input type="text" :placeholder="placeholder" ref="inputText" v-model="tagString" @input="input" />
            </div>
            <div class="suggestions" v-show="suggestions.length > 0">
                <ul>
                    <li v-for="suggestion in suggestions" @click="autocomplete(suggestion)">{{suggestion}}</li>
                </ul>
            </div>
        </div>
    </div>
</template>


<script>

    import CloseIcon from  'vue-material-design-icons/Close.vue';
	export default {
		props: [
			'model',
			'title',
			'hint',
			'onChangeCallback',
            'tags',
            'autoCompleteMethod',
            'placeholder',
            'noTagsLabel',
		],
		data: () => {
			return {
				editMode: false,
                tagString : '',
                suggestions : [],
			}
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
                this.$nextTick(()=>{
					this.$refs.inputText.focus();
                });
            },
            remove : function(tag) {
			   this.tags.splice(this.tags.indexOf(tag),1);
                this.onChange(this.tags);
            },
            input : async function() {
                let tags = this.tagString.split(',');
                if(tags.length == 1) {
                    if (typeof this.autoCompleteMethod === 'function') {
                        this.suggestions = await this.autoCompleteMethod(tags[0]);
                    }
                }
                else {
                    this.tags.push(tags.shift());
                    this.tagString = '';
                    this.suggestions = [];
                    this.onChange(this.tags);

                }
            },
            autocomplete : function(suggestion) {
                this.tags.push(suggestion);
                this.onChange(this.tags);
                this.suggestions = [];
                this.$nextTick(()=>{
                    this.tagString = '';
                    this.$refs.inputText.focus();
                });
            },

		},
        components: {CloseIcon}

	}
</script>
