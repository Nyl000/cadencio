<template>
    <div class="tag_selector">
        <span class="">{{currentTitle}}</span>
        <md-divider></md-divider>
        <md-autocomplete
                ref="autocomplete"
                v-model="selection"
                :md-options="suggestions"
                @md-selected="selectTag"
                @md-changed="handleSelection"
                @md-opened="loadSuggestions"
                :md-dense="this.$props.dense"
        >
            <label>Rechercher</label>
            <template slot="md-autocomplete-item" slot-scope="{ item }">
                <slot name="tag-selector-autocomplete-item" :item="item">{{item}}</slot>
            </template>
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
            },
            readOnly : {
                type : Boolean,
                required : false
            },
            dense : {
                type : Boolean,
                required : false
            },
            tagFormat : {
                type : Array,
                required : false
            },
        },
        data: () => {
            return {
                selection           : '',
                tags                : [],
                suggestions         : [],
                loadedSuggestions   : [],
                currentTitle        : 'Sélectionner les tags correspondants'
            }
        },

        mounted : async function() {
            this.loadTitle();
        },

        methods: {

            loadTitle :  function(){
                if(this.$props.title && this.$props.title !== ''){
                    this.currentTitle  = this.$props.title;
                }
            },

            loadSuggestions : function(){
                this.suggestions = new Promise(resolve => {
                    window.setTimeout(() => {
                        resolve(this.autoCompleteMethod(this.selection));
                    },500);
                })
            },

            selectTag : function(tag){

                let tagFormatted = tag;

                // tag can be string OR object
                if(this.$props.tagFormat && typeof(tag) === "object" ){
                    tagFormatted = '';
                    this.$props.tagFormat.forEach(element =>{
                        if(tag[element]){
                            tagFormatted += tag[element];
                        }else{
                            tagFormatted += element;
                        }
                    });
                }

                if(!this.value.includes(tagFormatted)){
                    let newTags = [...this.value,tagFormatted];
                    this.update(newTags);
                    this.clearSelection();
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

                if(!this.$props.readOnly){
                    if(searched && searched.length > 2){
                        const lastChar = searched.trim().substring((searched.length) -1,searched.length);

                        if(lastChar === ','){
                            let tag = searched.substring(0,searched.length -1);
                            if(!this.value.includes( tag)){
                                this.selectTag(tag);
                            }
                            this.clearSelection();
                        }
                    }else if(searched && searched.length === 0 ){
                        this.$refs.autocomplete.$el.querySelector('input').blur();
                    }
                }

                this.loadSuggestions();

            },

            update : function(newTags){
                this.$emit('input', newTags);
                this.$emit('change', newTags);
                this.loadSuggestions();
            },


        },
        components: {}

    }
</script>