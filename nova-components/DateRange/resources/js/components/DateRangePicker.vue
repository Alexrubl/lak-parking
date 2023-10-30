<script>
import flatpickr from 'flatpickr'
import 'flatpickr/dist/themes/airbnb.css'
import { Russian } from "flatpickr/dist/l10n/ru.js"

export default {
    props: {
        field: {
            required: true,
        },
        value: {
            required: false,
        },
        placeholder: {
            type: String,
            default: () => {
                return moment().format('DD.MM.YYYY') + ` ${this.default.props.seperator.default} ` + moment().format('DD.MM.YYYY')
            },
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        dateFormat: {
            type: String,
            default: 'd.m.Y',
        },
        seperator: {
            type: String,
            default: '-',
        },
        firstDayOfWeek: {
          type: Number,
          default: 1
        },
        locale: {
            type: String,
            default: Russian,
        },
        mode: {
            type: String,
            default: 'range',
        },
        enableTime: {
            type: Boolean,
            default: false,
        },
        noCalendar: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            flatpickr: null
        }
    },
    mounted() {
        console.log('mode', this.mode)
        Russian.rangeSeparator = ` ${this.seperator} `
        Russian.firstDayOfWeek = this.firstDayOfWeek
        this.$nextTick(() => {
            this.flatpickr = flatpickr(this.$refs.datePicker, {
                onClose: this.onChange,
                dateFormat: this.dateFormat,
                enableTime: this.enableTime,
                noCalendar: this.noCalendar,
                time_24hr: true,
                allowInput: true,
                mode: this.mode,
                locale: Russian
            })
        })        
    },

    methods: {        
        onChange(event) {
            this.$emit('change', { target: this.$refs.datePicker })
        },
    },
}
</script>

<template>
  <input
    :disabled="disabled"
    :dusk="field.attribute"
    :class="{'!cursor-not-allowed': disabled}"
    :value="value"
    :name="field.name"
    ref="datePicker"
    type="text"
    :placeholder="placeholder"
    :locale="locale"
    :mode="mode">
</template>

<style scoped>
.\!cursor-not-allowed {
    cursor: not-allowed !important;
}
</style>