<template>
    <div class="cadencio_imagefield">
        <div>
            <img @click="openDialog" class="image-preview" v-if="value" :src="value" />
        </div>
        <md-button class="md-primary" @click="openDialog">{{ value ? $t('Update picture') : $t('Send picture') }}
        </md-button>
        <div v-show="false">
            <input type="file" @change="imageSelectedChange" ref="fileInput" accept="image/*"/>
        </div>
        <md-dialog class="cadencio_imagefield_dialog" :md-active.sync="showUploadDialog">
            <md-dialog-title>Recadrer</md-dialog-title>
            <md-dialog-content>
                <div class="cropper_wrap">
                    <cropper
                        :stencilProps="{
                            minAspectRatio: 1,
                            maxAspectRatio : 2,

                        }"
                        image-restriction="stencil"

                        v-if="imageTmp" :src="imageTmp"
                        @change="onCrop"
                />
                </div>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-primary" :disabled="!this.imageTmp" @click="imageUploadDone()">{{$t('Select this picture')}}
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
                imageTmpName: '',
                imageTmp: false,
                imageFinal : false,
            }
        },
        mounted() {
        },
        methods: {

            openDialog : function() {
                this.$refs.fileInput.click();

            },
            imageUploadDone: function () {
                this.showUploadDialog = false;
                this.$emit('input',this.imageFinal);
                this.$emit('change',this.imageFinal);

            },

            imageSelectedChange: function ($event) {
                this.imageFinal = false;
                this.imageTmp = false;

                let fileList= $event.target.files;
                if (typeof fileList[0] !== 'undefined') {
                    var reader = new FileReader();
                    reader.onloadend = () => {
                        this.imageTmp = reader.result;
                        this.imageFinal = reader.result;
                        this.showUploadDialog = true;

                    };
                    reader.readAsDataURL(fileList[0]);
                }
            },
            onCrop : function({ coordinates, canvas }) {
                this.imageFinal = canvas.toDataURL();
            }

        },
        components : {
            Cropper,
        }
    }
</script>