<template>
  <Card ref="card" class="flex flex-col" :style="{height:heightCard + 'px'}">
    <div class="px-3 py-3 h-full">
      <video-player :options="videoOptions" class="" />      
    </div>    
    <div class="flex px-3 pb-3">
      <a size="md" href="#" class="flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold" >
        <span class="hidden md:inline-block">Открыть</span><span class="inline-block md:hidden">Открыть</span>
      </a>
         
    <div class="inline-flex items-center space-x-2 ml-auto">
      <a size="md" href="#" class="flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-red-500 hover:bg-red-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold" >
        <span class="hidden md:inline-block">Закрыть</span><span class="inline-block md:hidden">Закрыть</span>
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
      heightCard: 0,
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
            src:
              '/1/file.m3u8',
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
      console.log('resize');
      var $ref = this.$refs.card.$el
      console.log($ref, $ref.offsetWidth);
      this.heightCard = $ref.offsetWidth * (9/16) + 58      
    }
  },

  mounted() {
    this.resizeEventHandler()
    // axios.get('/api/test_234').then(response => {
    //   console.log(response)
    // })
    //console.log(axios);
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
