<template>
  <Card ref="card" class="flex flex-col" :style="{height:heightCard + 'px'}">
    <div class="px-3 py-3 h-full">
      <video-player :options="videoOptions" class="" />      
    </div>    
    <div class="flex px-3 pb-3">
      <a @click="clickOpen" size="md" href="#" class="flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold text-sm flex-shrink-0 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold" >
        <span class="hidden md:inline-block">Открыть проезд</span><span class="inline-block md:hidden">Открыть проезд</span>
      </a>
    </div>
    <div class="flex logs px-3 pb-3">
      <div>
        <h5>10.10.2023 10:00</h5>
        <p>Тестовове событие</p>
      </div>
      <div class="flex-grow"></div>
      <div class="flex items-center">
        <a
          class="flex-shrink-0 h-6 px-2 hover:bg-gray-100 dark:hover:bg-gray-700 h-10 focus:outline-none focus:ring rounded-lg flex items-center text-sm font-semibold text-gray-600 dark:text-gray-400" 
          href="#">
            <svg class="flex-shrink-0" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6">
              <path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path>
            </svg>
        </a>
      </div>      
    </div>            
  </Card>
</template>

<script>
import VideoPlayer from './VideoPlayer.vue';
import axios from 'axios'

export default {
  props: [
    'card'
    // The following props are only available on resource detail cards...
    // 'resource',
    // 'resourceId',
    // 'resourceName',
  ],
  components: {
    VideoPlayer
  },
  data() {
    return {
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
      }
    }
  },
  created() {
    window.addEventListener("resize", this.resizeEventHandler);
  },
  methods: {
    resizeEventHandler(e) {
      var $ref = this.$refs.card.$el
      this.heightCard = $ref.offsetWidth * (9/16) + 58      
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
      axios.post('/api/closeGate').then(resp => {
        console.log(resp);
      })
    }
  },

  mounted() {
    this.resizeEventHandler()
    // axios.get('/api/test_234').then(response => {
    //   console.log(response)
    // })
    console.log(this.card.controller, this.card.camera);
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
