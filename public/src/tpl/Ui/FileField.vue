<template>
    <div class="cadencio_filefield">
        <div>
            {{fileTmpName}}
        </div>
        <md-button class="md-primary" @click="openDialog">{{ value ? $t('Replace file') : $t('Send file') }}
        </md-button>
        <div v-show="false">
            <input type="file" @change="fileSelectedChange" ref="fileInput" accept="*/*"/>
        </div>
        <md-dialog class="cadencio_filefield_dialog" :md-active.sync="showUploadDialog">
            <md-dialog-actions>
                <md-button class="md-primary" :disabled="!this.imageTmp" @click="fileUploadDone()">{{$t('Select this file')}}
                </md-button>
            </md-dialog-actions>
        </md-dialog>


    </div>
</template>


<script>
    import { Cropper } from 'vue-advanced-cropper';
    import 'vue-advanced-cropper/dist/style.css';

    export default {
        props: {
            'value': {required: true, 'default': ''}
        },
        data: () => {
            return {
                showUploadDialog: false,
                fileTmpName: '',
                fileFinal : false,
            }
        },
        mounted() {
        },
        methods: {

            openDialog : function() {
                this.$refs.fileInput.click();

            },
            fileUploadDone: function () {
                this.showUploadDialog = false;
                this.$emit('input',this.fileFinal);
                this.$emit('change',this.fileFinal);

            },

            fileSelectedChange: function ($event) {
                this.fileFinal = false;

                let fileList= $event.target.files;
                if (typeof fileList[0] !== 'undefined') {
                    var reader = new FileReader();
                    reader.onloadend = () => {

                        this.fileFinal = reader.result;
                        this.fileUploadDone();

                    };
                    reader.readAsDataURL(fileList[0]);
                }
            }
        },
        components : {
            Cropper,
        }
    }
</script>