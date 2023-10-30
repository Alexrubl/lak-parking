<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <input
        :id="field.attribute"
        type="time"
        class="form-control form-input form-input-bordered"
        :class="errorClasses"
        :placeholder="field.name"
        v-model="value"
      />
      <span class="mx-2"> - </span>

      <input
        :id="field.attribute"
        type="time"
        class="form-control form-input form-input-bordered"
        :class="errorClasses"
        :placeholder="field.name"
        v-model="value2"
      />
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  data() {
    return {
      value2: '19:00',
    }
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value.split(' - ')[0] || '08:00'
      this.value2 = this.field.value.split(' - ')[1] || '19:00'
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      const regExp = /\*|:|#|&|\$/g;
      formData.append(this.fieldAttribute, [this.value, this.value2] || '')
    },
  },
}
</script>
