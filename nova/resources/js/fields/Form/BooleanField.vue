<template>
  <DefaultField
    :field="currentField"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <Checkbox
        class="mt-2"
        @input="toggle"
        :id="currentField.uniqueKey"
        :name="field.name"
        :checked="checked"
        :disabled="currentlyIsReadonly"
      />
    </template>
  </DefaultField>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from '@/mixins'

export default {
  mixins: [HandlesValidationErrors, DependentFormField],

  methods: {
    /*
     * Set the initial value for the field
     */
    setInitialValue() {
      this.value = this.currentField.value ?? this.value
    },

    /**
     * Return the field default value.
     */
    fieldDefaultValue() {
      return false
    },

    /**
     * Provide a function that fills a passed FormData object with the
     * field's internal value attribute
     */
    fill(formData) {
      this.fillIfVisible(formData, this.fieldAttribute, this.trueValue)
    },

    toggle() {
      this.value = !this.value

      if (this.field) {
        this.emitFieldValueChange(this.fieldAttribute, this.value)
      }
    },
  },

  computed: {
    checked() {
      return Boolean(this.value)
    },

    trueValue() {
      return +this.checked
    },
  },
}
</script>
