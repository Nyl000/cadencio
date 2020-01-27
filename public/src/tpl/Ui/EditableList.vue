<template>
    <transition name="fade">
        <div class="editable">
            <div v-show="editMode">
                <select ref="input" v-model="val" v-on:blur="leaveEditmode" v-on:change="leaveEditmode">
                    <option v-for="(item,key) in list" v-bind:value="key">
                        {{item}}
                    </option>
                </select>
            </div>
            <div v-show="!editMode" v-on:click="enterEditmode">
                {{list[val]}}
                <span class="placeholder" v-if="val === null">{{placeholder}}</span>
                <check-bold-icon v-if="success" class="icon success" />
                <close-icon v-if="error" class="icon error" />
            </div>
        </div>
    </transition>
</template>

<script>
    import Rest from 'js/Services/Rest';
    import Editable from 'tpl/Ui/EditableMixin.vue';
    import CheckBoldIcon from 'vue-material-design-icons/CheckBold.vue';
    import CloseIcon from 'vue-material-design-icons/Close.vue';

    export default {
        mixins: [Editable],
        components: {CheckBoldIcon,CloseIcon}


    }
</script>