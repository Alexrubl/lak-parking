<template>
  <Card class="flex flex-col justify-center bg-transparent">
    <div class="px-3 py-3">
      <DefaultButton @click="clickGuestPass()" class="mr-2">
        <span class="inline-block">Создать гостевой пропуск</span>
      </DefaultButton>
    </div>

    <Modal :show="showModalOpen" modal_style="window" size="4xl">
      <ModalHeader
        v-text="'Новый гостевой пропуск'"
        class="bg-gray-100 dark:bg-gray-700"
      />
      <ModalContent
        class="bg-gray-100 dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600"
      >
        <div
          class="space-y-2 md:flex @md/modal:flex md:flex-row @md/modal:flex-row md:space-y-0 @md/modal:space-y-0 py-5"
          index="0"
        >
          <div
            class="w-full px-6 md:mt-2 @md/modal:mt-2 md:px-8 @md/modal:px-8 md:w-1/5 @md/modal:w-1/5"
          >
            <label
              for="name-sozdat-transport-text-field"
              class="inline-block leading-tight space-x-1"
            >
              <span>Наименование</span>
              <span class="text-red-500 text-sm">*</span>
            </label>
          </div>
          <div
            class="w-full space-y-2 px-6 md:px-8 @md/modal:px-8 md:w-3/5 @md/modal:w-3/5"
          >
            <div class="space-y-1">
              <input
                v-model="transport.name"
                type="text"
                placeholder="Наименование"
                class="w-full form-control form-input form-input-bordered"
                id="name-sozdat-transport-text-field"
                dusk="name"
                maxlength="-1"
              />
            </div>
          </div>
        </div>

        <div class="space-y-2 md:flex @md/modal:flex md:flex-row @md/modal:flex-row md:space-y-0 @md/modal:space-y-0 py-5" index="1">
          <div class="w-full px-6 md:mt-2 @md/modal:mt-2 md:px-8 @md/modal:px-8 md:w-1/5 @md/modal:w-1/5">
            <label for="driver-sozdat-transport-text-field" class="inline-block leading-tight space-x-1">
              <span>Водитель</span>
            </label>
          </div>
          <div class="w-full space-y-2 px-6 md:px-8 @md/modal:px-8 md:w-3/5 @md/modal:w-3/5">
            <div class="space-y-1">
              <input 
                v-model="transport.driver"
                type="text"
                placeholder="Водитель"
                class="w-full form-control form-input form-input-bordered"
                id="driver-sozdat-transport-text-field"
                dusk="driver"
                maxlength="-1"
              />
            </div>            
          </div>
        </div>

        <div
          class="space-y-2 md:flex @md/modal:flex md:flex-row @md/modal:flex-row md:space-y-0 @md/modal:space-y-0 py-5"
          index="2"
        >
          <div
            class="w-full px-6 md:mt-2 @md/modal:mt-2 md:px-8 @md/modal:px-8 md:w-1/5 @md/modal:w-1/5"
          >
            <label
              for="number-sozdat-transport-text-field"
              class="inline-block leading-tight space-x-1 mb-2"
            >
              <span>Номер ТС</span><span class="text-red-500 text-sm">*</span>
            </label>
          </div>
          <div
            class="w-full space-y-2 px-6 md:px-8 @md/modal:px-8 md:w-3/5 @md/modal:w-3/5"
          >
            <div class="space-y-1">
              <input
                v-model="transport.number"
                type="text"
                placeholder="Номер ТС"
                class="w-full form-control form-input form-input-bordered"
                id="number-sozdat-transport-text-field"
                dusk="number"
                maxlength="-1"
              />
            </div>
            <p class="help-text">на английской раскладке</p>
          </div>
        </div>

        <div
          class="space-y-2 md:flex @md/modal:flex md:flex-row @md/modal:flex-row md:space-y-0 @md/modal:space-y-0 py-5"
          index="3"
        >
          <div
            class="w-full px-6 md:mt-2 @md/modal:mt-2 md:px-8 @md/modal:px-8 md:w-1/5 @md/modal:w-1/5"
          >
            <label
              for="type-sozdat-transport-belongs-to-field"
              class="inline-block leading-tight space-x-1"
              ><span>Тип ТС</span><span class="text-red-500 text-sm">*</span></label
            >
          </div>
          <div
            class="w-full space-y-2 px-6 md:px-8 @md/modal:px-8 md:w-3/5 @md/modal:w-3/5"
          >
            <div class="flex items-center space-x-2">
              <div class="flex relative w-full">
                <select
                  v-model="transport.typeTransport_id"
                  data-testid="type-transports"
                  dusk="type-transports-select"
                  class="w-full block form-control form-select form-select-bordered"
                >
                  <option disabled="" value="">—</option>
                  <option value="3">Более 3,5 тонн</option>
                  <option value="2">До 3,5 тонн</option>
                  <option value="1">Легковой</option></select
                ><svg
                  class="flex-shrink-0 pointer-events-none form-select-arrow"
                  xmlns="http://www.w3.org/2000/svg"
                  width="10"
                  height="6"
                  viewBox="0 0 10 6"
                >
                  <path
                    class="fill-current"
                    d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"
                  ></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-2 md:flex @md/modal:flex md:flex-row @md/modal:flex-row md:space-y-0 @md/modal:space-y-0 py-5" index="4">
            <div class="w-full px-6 md:mt-2 @md/modal:mt-2 md:px-8 @md/modal:px-8 md:w-1/5 @md/modal:w-1/5">
                <label for="tenant-obnovlenie-transport-toyota-prius-phv-belongs-to-field" class="inline-block leading-tight space-x-1">
                    <span>Арендатор</span>
                    <span class="text-red-500 text-sm">*</span>
                </label>
            </div>
            <div class="w-full space-y-2 px-6 md:px-8 @md/modal:px-8 md:w-3/5 @md/modal:w-3/5">
                <div class="flex items-center space-x-2">
                    <div class="flex relative w-full">
                        <model-list-select 
                          class="form-control form-select form-select-bordered"
                          :list="tenants"
                          v-model="tenantItem"
                          option-value="id"
                          option-text="name"
                          placeholder="наберите наименование арендатора"
                          @searchchange="searchTenant"
                          @update:modelValue="onSelectTenant">
                        </model-list-select>
                    </div>
                </div>
            </div>            
        </div>

        <p>{{ transport }}</p>
      </ModalContent>
      <ModalFooter>
        <div class="flex items-center ml-auto">
          <DefaultButton
            class="mr-2"
            :disabled="validate"
            @click="
              clickCreate();
              showModalOpen = false;
            "
          >
            Создать
          </DefaultButton>
          <BasicButton type="submit" @click="showModalOpen = false"> Отмена </BasicButton>
        </div>
      </ModalFooter>
    </Modal>
  </Card>
</template>

<script>
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from "vue-search-select"
import axios from 'axios'

export default {
  props: [
    "card",

    // The following props are only available on resource detail cards...
    // 'resource',
    // 'resourceId',
    // 'resourceName',
  ],
  components: {
    ModelListSelect
  },
  data() {
    return {
      showModalOpen: false,
      transport: {
        name: '',
        driver: '',
        number: '',
        typeTransport_id: null,
        tenant_id: null,
      },
      tenants: [],
      tenantItem: {}
    };
  },
  computed: {
    validate() {
      return true;
    },
  },
  methods: {
    clickCreate() {},
    clickGuestPass() {
      this.showModalOpen = true;
      console.log("clickGuestPass");
    },
    onSelectTenant(item) {
            console.log(item.name);
            axios.get('/api/search/tenant/' + item.name.trim()).then(response => {
                console.log(response.data);
            }).catch(error => {
                console.log(error.response);
            });
        },
    searchTenant(searchText) {
        console.log(searchText);
        if(searchText.trim().length > 2) {
            axios.get('/api/search/tenant/' + searchText.trim()).then(response => {
                this.tenants = response.data.tenants;
                console.log(response.data);
            }).catch(error => {
                console.log(error.response);
            });
        }
    },
  },
  mounted() {},
};
</script>

<style>
.dark .ui.selection.dropdown {
  background-color: rgba(var(--colors-gray-900)) !important;
  color: rgba(var(--colors-gray-400)) !important;
}
.ui.selection.dropdown {
  /* border-color: rgba(var(--colors-gray-300)) !important; */
  border-width: 1px !important;
}

/* .ui.selection.dropdown:hover {
  border-color: unset !important;
} */

.ui.selection.dropdown:focus, .ui.selection.dropdown:hover {
  /* border-color: rgba(var(--colors-primary-300)) !important; */
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color) !important;
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color) !important;
  box-shadow: var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000) !important;
  outline: 2px solid transparent !important;
  outline-offset: 2px !important;
}

.ui.selection.dropdown:focus, .ui.selection.active.dropdown:focus, .ui.selection.dropdown:hover, .ui.selection.active.dropdown:hover, .menu {
  border-color: rgba(var(--colors-primary-300)) !important;
}

.dark .ui.dropdown.selected, .dark .ui.dropdown .menu .selected.item {
    background-color: rgba(var(--colors-gray-900)) !important;
    color: rgba(var(--colors-gray-400)) !important;
}

.dark .ui.selection.dropdown .menu>.item {
    border-color: rgba(var(--colors-gray-500)) !important;
}

.dark .ui.selection.dropdown:focus, .dark .ui.selection.active.dropdown:focus, .dark .ui.selection.dropdown:hover, .dark .ui.selection.active.dropdown:hover, .dark .menu {
      border-color: rgba(var(--colors-gray-500)) !important;
}
</style>
