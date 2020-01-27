<template>
    <transition name="fade">
        <div class="editable">

            <div>
                <span>{{val === 'Invalid date' ? '-' : val}}</span>
            </div>
        </div>
    </transition>
</template>


<script>
    import 'vue-datetime/dist/vue-datetime.css';
    import moment from 'moment-timezone';
    import {getUserOption} from 'js/Models/User';

    export default {
        props: ['value', 'list', 'saveurl', 'id', 'field', 'canupdate','placeholder','link'],
        data: function () {
            return {
                val: this.value == null ? '' : moment.tz(this.value,'UTC').clone().tz(getUserOption('timezone'|| 'UTC')).fromNow(),
            }
        },
    }
</script>