<template>
    <div class="tag_selector">
        <span class="">{{currentTitle}}</span>
        <md-divider></md-divider>
        <md-autocomplete
                ref="autocomplete"
                v-model="selection"
                :md-options="suggestions"
                @md-selected="selectTag"
                @md-closed="clearSelection"
                @md-changed="handleSelection"

        >
            <label>Rechercher</label>
        </md-autocomplete>
        <md-chip  v-for="tag in this.$props.value" md-deletable @md-delete="removeTag(tag)">{{tag}}</md-chip>
    </div>
</template>

<script>

    export default {
        props: {
            value : {
                type : Array,
                required : true
            },
            autoCompleteMethod : {
                type : Function,
                required : false
            },
            title : {
                type : String,
                required : false
            }
        },
        data: () => {
            return {
                selection    : '',
                tags         : [],
                suggestions  : [],
                currentTitle : 'Sélectionner les tags correspondants'
            }
        },

        mounted : async function() {
            this.loadSuggestions();
            this.loadTitle();
        },

        methods: {

            loadTitle :  function(){
                if(this.$props.title && this.$props.title !== ''){
                    this.currentTitle  = this.$props.title;
                }
            },

            loadSuggestions : async function(){
                let suggestions  = await this.autoCompleteMethod();
                this.suggestions = suggestions.filter( ( el ) => !this.value.includes( el ) );
            },

            selectTag : function(tag){
                if(!this.value.includes( tag)){
                    let newTags = [...this.value,tag];
                    this.update(newTags);
                }
            },

            clearSelection : function(){
                this.selection = '';
            },

            removeTag : function(tag){
                let newTags = [...this.value];
                newTags.splice(newTags.indexOf(tag),1);
                this.update(newTags);
                this.loadSuggestions();
            },

            handleSelection : function(searched){
                if(searched.length > 2){
                    const lastChar = searched.trim().substring(searched.length -1,searched.length);

                    if(lastChar === ','){
                        let tag = searched.substring(0,searched.length -1);
                        if(!this.value.includes( tag)){
                            this.selectTag(tag);
                        }
                        this.clearSelection();
                    }
                }else{
                    this.$ref.autocomplete.blur();
                }

            },

            update : function(newTags){
                this.$emit('input', newTags);
                this.$emit('change', newTags);
                this.loadSuggestions();
            }

        },
        components: {}

    }
</script>
