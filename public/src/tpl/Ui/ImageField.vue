<template>
    <div class="cadencio_imagefield">
        <div>
            <img @click="openDialog" class="image-preview" v-if="value" :src="value" />
        </div>
        <md-button :disabled="disabled" class="md-primary" @click="openDialog">{{ value ? $t('Update picture') : $t('Send picture') }}
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
                            minAspectRatio: this.resize_min_ratio,
                            maxAspectRatio : this.resize_max_ratio,
                        }"
                        image-restriction="stencil"
                        v-if="imageTmp" :src="imageTmp"
                        @change="onCrop"
                    />
                </div>
            </md-dialog-content>
            <md-dialog-actions>
                <md-button class="md-primary" :disabled="!imageTmp || disabled" @click="imageUploadDone()">{{$t('Select this picture')}}
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
            'value': {required: true, 'default': ''},
            'resize_min_ratio': {required: false, 'default': 1},
            'resize_max_ratio': {required: false, 'default': 2},
            'disabled' : {required : false, default : false}
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
                if (this.disabled) return;

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
                        if ( !/svg\+xml/.test(reader.result)) {
                            this.showUploadDialog = true;
                        }
                        else {
                            this.imageUploadDone();
                        }
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