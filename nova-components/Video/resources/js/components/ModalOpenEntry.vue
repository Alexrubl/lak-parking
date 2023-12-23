<template>
    <DefaultButton @click="showModal()" class="mr-2">
        <span class="inline-block">Открыть проезд ...</span>
    </DefaultButton>

    <Modal :show="showModalOpen" modal_style="window" size="4xl">
      <ModalHeader v-text="'Открытие проезда для арендатора'" class="bg-gray-100 dark:bg-gray-700" />
      <ModalContent class="bg-gray-100 dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
            <div class="space-y-2 md:flex @md/modal:flex md:flex-row @md/modal:flex-row md:space-y-0 @md/modal:space-y-0 py-5" index="4">
                <div class="w-full px-6 md:mt-2 @md/modal:mt-2 md:px-8 @md/modal:px-8 md:w-1/5 @md/modal:w-1/5">
                    <label for="tenant-obnovlenie-transport-toyota-prius-phv-belongs-to-field" class="inline-block leading-tight space-x-1">
                        <span>Номер Транспорта</span>
                        <!-- <span class="text-red-500 text-sm">*</span> -->
                    </label>
                </div>
                <div class="w-full space-y-2 px-6 md:px-8 @md/modal:px-8 md:w-3/5 @md/modal:w-3/5">
                    <div class="flex items-center space-x-2">
                        <div class="flex relative w-full">
                            <!-- <test></test> -->
                            <model-select 
                                ref="transport"
                                :options="transports"
                                v-model="transportItem"
                                option-value="id"
                                option-text="name"
                                placeholder="введите номер транспорта"
                                @select="onSelect"
                                @searchchange="searchTransport"
                                @update:modelValue="onSelectTransport">
                            </model-select>                            
                        </div>                        
                    </div>                    
                </div>                         
            </div>
            <!-- <p>{{ transportItem }}</p>    -->
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
                            <model-list-select :list="tenants"
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
        <!-- <model-list-select :list="transports"
                    v-model="transportItem"
                    option-value="id"
                    option-text="name"
                    :custom-text="customTrasportText"
                    placeholder="введите номер транспорта"
                    @searchchange="searchTransport"
                    @update:modelValue="onSelectTransport">
        </model-list-select>
        <p>{{ transportItem }}</p>

        <model-list-select :list="tenants"
                     v-model="tenantItem"
                     option-value="id"
                     option-text="name"
                     placeholder="наберите наименование арендатора"
                     @searchchange="searchTenant"
                     @update:modelValue="onSelectTenant">
        </model-list-select>
        <p>{{ tenantItem }}</p> -->



      </ModalContent>
      <ModalFooter>
        <div class="flex items-center ml-auto">
            <DefaultButton
                class="mr-2"
                :disabled="!allowOpen"
                @click="clickOpen();showModalOpen = false" 
            >
                Открыть проезд
            </DefaultButton>
            <BasicButton
                type="submit" 
                @click="showModalOpen = false"
            >
                Закрыть
            </BasicButton>
        </div>
      </ModalFooter>
    </Modal>  
</template>

<script>
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from "vue-search-select"
import { ModelSelect } from "vue-search-select"
import test from './test'
import axios from 'axios';

export default {
    props: {
        controller_id: {
            type: Number,
        },
        show: {
            type: Boolean,
            default: false
        }
    },
    components: {
        ModelListSelect,
        ModelSelect,
        test
    },
    data() {
        return {
            allowOpen: false,
            showModalOpen: false,
            selected: null,
            transports: [
            ],
            transportItem: {},
            tenants: [
            ],
            tenantItem: {},
        }
    },
    methods: {
        showModal() {
            this.showModalOpen = false;
            this.transportItem = {};
            this.tenantItem ={} 
            this.showModalOpen = true;              
            setTimeout(() => {
               this.$refs.transport.openOptions();  
            }, 500);                       
        },
        closeModal() {
            this.showModalOpen = false;
            this.transportItem = {};
            this.tenantItem ={}                     
        },
        onSelect(items, lastSelectItem) {
            console.log('select', items);
        },
        onSelectTransport(item) {
            console.log(item);
            this.tenantItem = this.tenants.filter(i => i.id == item.tenant_id)[0]
        },
        searchTransport(searchText) {
            if(searchText.trim().length > 2) {
                axios.get('/api/search/transport/' + searchText.trim()).then(response => {
                    this.transports = response.data.transports;
                    this.tenants = response.data.tenants;
                   console.log(response.data);
                }).catch(error => {
                    console.log(error.response);
                });
            }
        },
        customTrasportText(item) {
            return `${item.number} - ${item.name}`
        },
        onSelectTenant(item) {
            console.log(item.name);
            axios.get('/api/search/tenant/' + item.name.trim()).then(response => {
                this.transports = response.data.transports;
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
                    this.transports = response.data.transports;
                   console.log(response.data);
                }).catch(error => {
                    console.log(error.response);
                });
            }
        },
        clickOpen(e) {
            axios.post('/api/openGate', {
                controller_id: this.controller_id,
                transport_id: this.transportItem.value,
                tenant_id: this.tenantItem.id
            }).then(resp => {
                if (resp.status == 200) {
                Nova.success('Команда отправлена.')
                }
            }).catch(err => {
                if (err.response.status == 503) {
                Nova.error('Команда не отправлена. Устройство недоступно.')
                } else {
                Nova.error('Команда не доставлена.')
                }
            })
        },
    },
    watch: {
        tenantItem(val) {
            if (val) {
                this.allowOpen = true
            }
        }
    },
    mounted() {
        //  setTimeout(() => {
        //         console.log('focus', this.$refs.transport);
        //         this.$refs.transport.openOptions();  
        //     }, 1000);
    },
}
</script>

<style>
/* select {
    border: 1px solid #444444;
    border-radius: 4px;
    padding: .2em .6em;
    margin-top: 10px;
    background: transparent;
    transition: background-color .5s;
}
select option {
    background: transparent;
} */
</style>
