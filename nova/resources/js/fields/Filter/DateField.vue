<template>
  <FilterContainer>
    <template #filter>
      <label class="block">
        <span class="uppercase text-xs font-bold tracking-wide">{{
          `${filter.name} - ${__('From')}`
        }}</span>
        <input
          class="flex w-full form-control form-input form-control-bordered"
          ref="startField"
          v-model="startValue"
          :dusk="`${field.uniqueKey}-range-start`"
          v-bind="startExtraAttributes"
        />
      </label>

      <label class="block mt-2">
        <span class="uppercase text-xs font-bold tracking-wide">{{
          `${filter.name} - ${__('To')}`
        }}</span>
        <input
          class="flex w-full form-control form-input form-control-bordered"
          ref="endField"
          v-model="endValue"
          :dusk="`${field.uniqueKey}-range-end`"
          v-bind="endExtraAttributes"
        />
      </label>
    </template>
  </FilterContainer>
</template>

<script>
import { DateTime } from 'luxon'
import debounce from 'lodash/debounce'
import omit from 'lodash/omit'
import filled from '@/util/filled'

export default {
  emits: ['change'],

  props: {
    resourceName: {
      type: String,
      required: true,
    },
    filterKey: {
      type: String,
      required: true,
    },
    lens: String,
  },

  data: () => ({
    startValue: null,
    endValue: null,
    debouncedHandleChange: null,
  }),

  created() {
    this.debouncedHandleChange = debounce(() => this.handleChange(), 500)
    this.setCurrentFilterValue()
  },

  mounted() {
    Nova.$on('filter-reset', this.handleFilterReset)
  },

  beforeUnmount() {
    Nova.$off('filter-reset', this.handleFilterReset)
  },

  watch: {
    startValue() {
      this.debouncedHandleChange()
    },

    endValue() {
      this.debouncedHandleChange()
    },
  },

  methods: {
    setCurrentFilterValue() {
      let [startValue, endValue] = this.filter.currentValue || [null, null]

      this.startValue = filled(startValue)
        ? this.fromDateTimeISO(startValue).toISODate()
        : null
      this.endValue = filled(endValue)
        ? this.fromDateTimeISO(endValue).toISODate()
        : null
    },

    validateFilter(startValue, endValue) {
      startValue = filled(startValue)
        ? this.toDateTimeISO(startValue, 'start')
        : null
      endValue = filled(endValue) ? this.toDateTimeISO(endValue, 'end') : null

      return [startValue, endValue]
    },

    handleChange() {
      this.$emit('change', {
        filterClass: this.filterKey,
        value: this.validateFilter(this.startValue, this.endValue),
      })
    },

    handleFilterReset() {
      this.$refs.startField.value = ''
      this.$refs.endField.value = ''

      this.setCurrentFilterValue()
    },

    fromDateTimeISO(value) {
      return DateTime.fromISO(value)
    },

    toDateTimeISO(value, range) {
      return DateTime.fromISO(value).toISODate()
    },
  },

  computed: {
    filter() {
      return this.$store.getters[`${this.resourceName}/getFilter`](
        this.filterKey
      )
    },

    field() {
      return this.filter.field
    },

    startExtraAttributes() {
      const attrs = omit(this.field.extraAttributes, ['readonly'])

      return {
        // Leave the default attributes even though we can now specify
        // whatever attributes we like because the old number field still
        // uses the old field attributes
        type: this.field.type || 'date',
        placeholder: this.__('Start'),
        ...attrs,
      }
    },

    endExtraAttributes() {
      const attrs = omit(this.field.extraAttributes, ['readonly'])

      return {
        // Leave the default attributes even though we can now specify
        // whatever attributes we like because the old number field still
        // uses the old field attributes
        type: this.field.type || 'date',
        placeholder: this.__('End'),
        ...attrs,
      }
    },
  },
}
</script>
