<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <date-range-picker
        class="w-full form-control form-input form-input-bordered"
        :name="field.name"
        :field="field"
        :value="value"
        :seperator="seperator"
        :enableTime="enableTime"
        :noCalendar="noCalendar"
        :mode="mode"
        :id="field.attribute"
        :firstDayOfWeek="firstDayOfWeek"
        :dateFormat="format"
        :placeholder="placeholder"
        @change="handleChange"
        :disabled="isReadonly"
      />
      <p v-if="hasError" class="my-2 text-danger">
          {{ firstError }}
      </p>
    </template>
  </DefaultField> 
</template>

<script>
import DateRangePicker from './DateRangePicker'
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import moment from 'moment';

export default {
  mixins: [FormField, HandlesValidationErrors],

  components: { DateRangePicker }, 

  props: ['resourceName', 'resourceId', 'field'],

  computed: {
      format() {
          return this.field.format
      },
      seperator() {
          return this.field.seperator
      },
      firstDayOfWeek() {
          return this.field.firstDayOfWeek || 1;
      },  
      locale() {
        return this.field.locale || 'Russian';
      },     
      enableTime() {
        return this.field.enableTime || false;
      }, 
      noCalendar() {
        return this.field.noCalendar || false;
      }, 
      mode() {
        return this.field.mode || 'range';
      }, 
      placeholder() {
        return moment().format('DD.MM.YYYY') + ` ${this.field.seperator} ` + moment().format('DD.MM.YYYY')
      },
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || moment().format('DD.MM.YYYY') + ' - 31.12.2119';
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      console.log(this.value)
      formData.append(this.fieldAttribute, this.value || null)
    },
  },
  mounted() { 
  },
}
</script>
