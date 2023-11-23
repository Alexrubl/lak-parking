<template>
    <!-- <div class="relative"> -->
        <video ref="videoPlayer" class="video-js vjs-live vjs-liveui"></video>
    <!-- </div> -->
    
</template>

<script>
import videojs from 'video.js';
import css from 'video.js/dist/video-js.css'

export default {
  name: 'VideoPlayer',
  props: {
    options: {
      type: Object,
      default() {
        return {};
      }
    }
  },
  data() {
    return {
      player: null
    }
  },
  mounted() {
    this.player = videojs(this.$refs.videoPlayer, this.options, () => {
      this.player.log('onPlayerReady', this);
    });
  },
  beforeDestroy() {
    if (this.player) {
      this.player.dispose();
    }
  }
}
</script>

<style lang="css">
    /* .video-js {
        position: relative !important;
        width: 100% !important;
        height: auto !important;
    } */
    /* .vjs-poster {
        position: relative !important;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    } */
</style>