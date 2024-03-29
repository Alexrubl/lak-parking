<template>
  <FilterContainer v-if="shouldShowFilter">
    <span>{{ filter.name }}</span>

    <template #filter>
      <SearchInput
        v-if="isSearchable"
        ref="searchable"
        :dusk="`${field.uniqueKey}-search-filter`"
        @input="performSearch"
        @clear="handleClearSelection"
        @shown="handleShowingActiveSearchInput"
        @selected="selectResource"
        :debounce="field.debounce"
        :value="selectedResource"
        :data="availableResources"
        :clearable="true"
        trackBy="value"
        class="w-full"
        mode="modal"
      >
        <div v-if="selectedResource" class="flex items-center">
          <div v-if="selectedResource.avatar" class="mr-3">
            <img
              :src="selectedResource.avatar"
              class="w-8 h-8 rounded-full block"
            />
          </div>

          {{ selectedResource.display }}
        </div>

        <template #option="{ selected, option }">
          <div class="flex items-center">
            <div v-if="option.avatar" class="flex-none mr-3">
              <img :src="option.avatar" class="w-8 h-8 rounded-full block" />
            </div>

            <div class="flex-auto">
              <div
                class="text-sm font-semibold leading-normal"
                :class="{ 'text-white dark:text-gray-900': selected }"
              >
                {{ option.display }}
              </div>

              <div
                v-if="field.withSubtitles"
                class="text-xs font-semibold leading-normal text-gray-500"
                :class="{ 'text-white dark:text-gray-700': selected }"
              >
                <span v-if="option.subtitle">{{ option.subtitle }}</span>
                <span v-else>{{ __('No additional information...') }}</span>
              </div>
            </div>
          </div>
        </template>
      </SearchInput>

      <SelectControl
        v-else-if="availableResources.length > 0"
        :dusk="`${field.uniqueKey}-filter`"
        v-model:selected="selectedResourceId"
        @change="selectedResourceId = $event"
        :options="availableResources"
        label="display"
      >
        <option value="" selected>&mdash;</option>
      </SelectControl>
    </template>
  </FilterContainer>
</template>

<script>
import debounce from 'lodash/debounce'
import find from 'lodash/find'
import isNil from 'lodash/isNil'
import { PerformsSearches } from '@/mixins'
import storage from '@/storage/ResourceSearchStorage'
import filled from '@/util/filled'

export default {
  emits: ['change'],

  mixins: [PerformsSearches],

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
    availableResources: [],
    selectedResource: null,
    selectedResourceId: '',
    softDeletes: false,
    withTrashed: false,
    search: '',

    debouncedHandleChange: null,
  }),

  mounted() {
    Nova.$on('filter-reset', this.handleFilterReset)

    this.initializeComponent()
  },

  created() {
    this.debouncedHandleChange = debounce(() => this.handleChange(), 500)

    Nova.$on('filter-active', this.handleClosingInactiveSearchInputs)
  },

  beforeUnmount() {
    Nova.$off('filter-active', this.handleClosingInactiveSearchInputs)
    Nova.$off('filter-reset', this.handleFilterReset)
  },

  watch: {
    selectedResource(resource) {
      this.selectedResourceId = filled(resource) ? resource.value : ''
    },

    selectedResourceId() {
      this.debouncedHandleChange()
    },
  },

  methods: {
    /**
     * Initialize the component.
     */
    initializeComponent() {
      let filter = this.filter
      let shouldSelectInitialResource = false

      if (this.filter.currentValue) {
        this.selectedResourceId = this.filter.currentValue

        if (this.isSearchable === true) {
          shouldSelectInitialResource = true
        }
      }

      if (!this.isSearchable || shouldSelectInitialResource) {
        this.getAvailableResources().then(() => {
          if (shouldSelectInitialResource === true) {
            this.selectInitialResource()
          }
        })
      }
    },

    /**
     * Get the resources that may be related to this resource.
     */
    getAvailableResources(search) {
      let queryParams = this.queryParams

      if (!isNil(search)) {
        queryParams.first = false
        queryParams.current = null
        queryParams.search = search
      }

      return storage
        .fetchAvailableResources(this.filter.field.resourceName, {
          params: queryParams,
        })
        .then(({ data: { resources, softDeletes, withTrashed } }) => {
          if (!this.isSearchable) {
            this.withTrashed = withTrashed
          }

          this.availableResources = resources
          this.softDeletes = softDeletes
        })
    },

    /**
     * Select the initial selected resource
     */
    selectInitialResource() {
      this.selectedResource = find(
        this.availableResources,
        r => r.value === this.selectedResourceId
      )
    },

    handleShowingActiveSearchInput() {
      Nova.$emit('filter-active', this.filterKey)
    },

    closeSearchableRef() {
      if (this.$refs.searchable) {
        this.$refs.searchable.close()
      }
    },

    handleClosingInactiveSearchInputs(key) {
      if (key !== this.filterKey) {
        this.closeSearchableRef()
      }
    },

    /**
     * Handle clear search selection
     */
    handleClearSelection() {
      this.clearSelection()
    },

    handleChange() {
      this.$emit('change', {
        filterClass: this.filterKey,
        value: this.selectedResourceId,
      })
    },

    handleFilterReset() {
      if (this.filter.currentValue !== '') {
        return
      }

      this.selectedResourceId = ''
      this.selectedResource = null
      this.availableResources = []

      this.closeSearchableRef()

      this.initializeComponent()
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

    shouldShowFilter() {
      return (
        this.isSearchable ||
        (!this.isSearchable && this.availableResources.length > 0)
      )
    },

    /**
     * Determine if the related resources is searchable
     */
    isSearchable() {
      return this.field.searchable
    },

    /**
     * Get the query params for getting available resources
     */
    queryParams() {
      return {
        current: this.selectedResourceId,
        first: this.selectedResourceId && this.isSearchable,
        search: this.search,
        withTrashed: this.withTrashed,
      }
    },
  },
}
</script>
