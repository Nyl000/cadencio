<template>
    <div class="cadencio_imagefield">
        <div>
            <img class="image-preview" v-if="value" :src="value" />
        </div>
        <md-button class="md-primary" @click="showUploadDialog = true">{{ value ? $t('Update picture') : $t('Send picture') }}
        </md-button>

        <md-dialog class="cadencio_imagefield_dialog" :md-active.sync="showUploadDialog">
            <md-dialog-title>Envoyer une image</md-dialog-title>
            <md-dialog-content>

                <img class="imagepreview" v-if="imageTmp" :src="imageTmp"/>

                <md-field>
                    <label>{{$t('Upload your picture')}}</label>
                    <md-file @md-change="imageSelectedChange" v-model="imageTmpName" accept="image/*"/>
                </md-field>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-primary" :disabled="!this.imageTmp" @click="imageUploadDone()">{{$t('Select this picture')}}
                </md-button>
            </md-dialog-actions>
        </md-dialog>

    </div>
</template>


<script>

    export default {
        props: {
            'value': {required: true, 'default': ''}
        },
        data: () => {
            return {
                showUploadDialog: false,
                imageTmpName: '',
                imageTmp: false,
            }
        },
        mounted() {
        },
        methods: {

            imageUploadDone: function () {
                this.showUploadDialog = false;
                this.$emit('input',this.imageTmp);
                this.$emit('change',this.imageTmp);

            },

            imageSelectedChange: function (fileList) {
                if (typeof fileList[0] !== 'undefined') {
                    var reader = new FileReader();
                    reader.onloadend = () => {
                        this.imageTmp = reader.result;
                    };
                    reader.readAsDataURL(fileList[0]);
                }
            }

        }
    }
</script>