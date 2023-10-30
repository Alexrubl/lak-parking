<template>
  <Card class="flex flex-col items-center justify-center">
    <div class="px-3 py-3">
      <h1 class="text-center text-3xl text-gray-500 font-light">Video</h1>
        <div> 
          <video ref="videoPlayer" class="video-js vjs-default-skin">
            <source :src="'ws:localhost:9999/rtsp?url='+ btoa(rtsp)">
          </video>
        </div>
    </div>
  </Card>
</template>

<script>

import rtsp2web from 'rtsp2web'
import videojs from 'video.js'
import "videojs-flash"; // 引入videojs flash

export default {
  name: 'VideoPlayer',
  props: [
    'card',

    // The following props are only available on resource detail cards...
    // 'resource',
    // 'resourceId',
    // 'resourceName',
  ],
  data() {
    return {
      player: null,
      rtspUrl: null
    }
  },

  methods: {
  },

  mounted() {
    new rtsp2web({ 9999 })
    this.rtsp = 'rtsp://test:123456789qQ@5.165.25.145:55554'
    this.player = videojs(this.$refs.videoPlayer, null, () => {
      this.player.log('onPlayerReady', this);
    });
  },
  beforeDestroy() {
    if (this.player) {
      this.player.dispose();
    }
  },
}
</script>
