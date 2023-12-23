<template>
  <Card ref="card" class="flex flex-col" :style="{height:heightCard + 'px'}">
    <div class="px-3 py-3 h-full">
      <video-player :options="videoOptions" class="" />      
    </div>    
    <div class="flex px-3 pb-3">
      <ModalOpenEntry :show="showModalOpen" :controller_id="card.controller.id"></ModalOpenEntry>
      <div class="flex-grow"></div>
      <LinkButton @click="clickOpen">
        <span class="inline-block">Откр. проезд</span>
      </LinkButton>
    </div>
    
    <div class="flex logs px-3 pb-3">
      <div style="min-height: 60px;">
        <h5 class="font-bold" v-html="formattedDate(message.created_at)"></h5>
        <p class="font-bold text-red-400" v-html="message.text"></p>
      </div>
      <div class="flex-grow"></div>
      <div class="flex items-center">
        <a
          @click="showModal = true"
          class="flex-shrink-0 h-6 px-2 hover:bg-gray-100 dark:hover:bg-gray-700 h-10 focus:outline-none focus:ring rounded-lg flex items-center text-sm font-semibold text-gray-600 dark:text-gray-400" 
          href="#">
            <svg class="flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6">
              <path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path>
            </svg>
        </a>
      </div>      
    </div>  
    <Modal :show="showModal" modal_style="window" size="4xl">
      <ModalHeader v-text="'Последние события'" class="bg-gray-100 dark:bg-gray-700" />
      <ModalContent class="bg-gray-100 dark:bg-gray-700">
        <EasyDataTable
          :headers="table.headers"
          :items="table.items"
          :rows-items="[10,15,20]"
          :rows-per-page="10"
          show-index
        >
          <template #item-created_at="{ created_at }">
              {{ formattedDate(created_at) }}
          </template>
        </EasyDataTable>
      </ModalContent>
      <ModalFooter>
        <div class="flex items-center ml-auto">
          <DefaultButton
              type="submit" 
              @click="showModal = false"
          >
            Закрыть
          </DefaultButton>
        </div>
      </ModalFooter>
    </Modal>  
    
    

  </Card>
</template>

<script>
import ModalOpenEntry from './ModalOpenEntry'
import test from './test'
import VideoPlayer from './VideoPlayer.vue';
import axios from 'axios'
import moment from 'moment'
import EasyDataTable from "vue3-easy-data-table";
import 'vue3-easy-data-table/dist/style.css';

export default {
  props: [
    'card'
    // The following props are only available on resource detail cards...
    // 'resource',
    // 'resourceId',
    // 'resourceName',
  ],
  components: {
    VideoPlayer,
    EasyDataTable,
    ModalOpenEntry
  },
  data() {
    return {
      message: {
        created_at: '',
        text: ''
      },
      alert: false,
      showModal: false,
      showModalOpen: false,
      logs: [],
      heightCard: 250,
      videoOptions: {
        autoplay: 'any',
        controls: true,
        liveui: true,
        // fluid: true,
        //aspectRatio: '16:9',
        fill: true,
        responsive: true,
        languages: {
          ru: {
            Play: 'Воспроизвести',
            Pause: 'Пауза',
            Mute: 'Звук выкл.'
          }
        },
        techOrder: ['flash', 'html5'],
        sources: [
          {
            src: this.card.camera.fields.url,
            type: 'application/x-mpegURL'
          }
        ]
      },
      table: {
          headers: [
            { text: "Дата", value: "created_at" },
            { text: "Текст", value: "text" }
          ],
          items: [
            { created_at: '', text: ''}
          ]
        },
    }
  },
  created() {
    window.addEventListener("resize", this.resizeEventHandler);
  },
  methods: {
    formattedDate(val) {
      return val ? moment(val).format('DD-MM-YYYY HH:mm') : '';
    },
    resizeEventHandler(e) {
      var $ref = this.$refs.card.$el
      this.heightCard = $ref.offsetWidth * (9/16) + 130      
    },
    clickOpen(e) {
      console.log('clickOpen');
      axios.post('/api/openGate', {
        controller_id: this.card.controller.id
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
    clickClose(e) {
      axios.post('/api/closeGate', {
        controller_id: this.card.controller.id
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
    async get_logs() {
      var { data } =  await axios.get('/api/getLogs', { params: { controller_id: this.card.controller.id, entry: this.card.camera.fields.entry } })
      if (data.length > 0) {
        this.table.items = data
      }
      this.message = this.table.items[0]
      console.log(moment(this.table.items[0].created_at).unix() >= moment().subtract(60, 'seconds').unix());
      if (moment(this.table.items[0].created_at).unix() >= moment().subtract(60, 'seconds').unix()) {
        
        if (this.alert == 0) {
          Nova.error(this.message.text)
          this.alert = 1
        }
      } else {
        //this.message.created_at = ''; this.message.text = ''
        this.alert = 0
      }
    }
  },

  mounted() {
    this.get_logs()
    setInterval(() => {
      this.get_logs()      
    }, 10000);
    
    this.resizeEventHandler()

    //console.log(this.card.controller, this.card.camera);
  },
  destroyed() {
    window.removeEventListener("resize", this.resizeEventHandler);
  },
}
</script>

<style>
    .vjs-poster2 {
        position: relative !important;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }
</style>
