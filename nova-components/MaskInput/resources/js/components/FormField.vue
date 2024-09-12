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
        type="text"
        class="w-full form-control form-input form-control-bordered"
        :class="errorClasses"
        :placeholder="field.name"
        v-model="value"
        v-maska
        :data-maska="mask"
        data-maska-tokens="A:[A-Z]|a:[a-z]|А:[А-Я]|а:[а-я]|Z:[a-zA-Z]"
      />
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import {vMaska} from 'maska/vue'

export default {
  mixins: [FormField, HandlesValidationErrors],

  directives: {maska: vMaska},

  props: ['resourceName', 'resourceId', 'field'],

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || ''
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.fieldAttribute, this.value || '')
    },
  },
  computed: {
    mask() {
      return this.field.mask;
    },
  },
}
</script>
