<template>
    <h1>Hello Vue!</h1>
    <div>
        Привет 1
    </div>
    <video src=""></video>
    <div id="myFlvVideo"></div>
    <div class="flvplayer-app"></div>
    <div>
        <video autoplay controls id="myVideoFlv" />
    </div>

    <div id="publish" style="width:320px;height:240px;border: solid 1px"></div>
    <br/><button id="publishBtn">Publish</button><br/>
    <br/>
    <div id="play" style="width:320px;height:240px;border: solid 1px"></div>
    <br/><button id="playBtn">Play</button><br/>
    <br/>
    <br/><button id="stopBtn">Stop</button><br/>
</template>

<script>
import flvjs from 'flv.js/dist/flv.min.js'
import * as Flashphoner from '@flashphoner/websdk';

export default {
    name: "viewer",
    data() {
        return {
            flvPlayer: null,
            webrtcUrl: "rtsp://test:123456789qQ@5.165.25.145:55554"
        }
    },

    methods: {
        init_api() {
            Flashphoner.init({});
            publishBtn.onclick = this.connect;
            playBtn.onclick = this.playStream;
            stopBtn.onclick = this.stopPublish;
        },
        connect() {
            session = Flashphoner.createSession({
                urlServer: "rtsp://test:123456789qQ@5.165.25.145:55554"
            }).on(SESSION_STATUS.ESTABLISHED, function(session) {
                publishStream(session);
            });
        },
        publishStream(session) {
            stream = session.createStream({
                name: "stream",
                display: document.getElementById("publish"),
            });
            stream.publish();
        },
        playStream() {
            session.createStream({
                name: "stream",
                display: document.getElementById("play"),
            }).play();
        },
        stopPublish() {
            stream.stop();
        },
        webrtcPlay() {
            if (flvjs.isSupported()) {
                var videoElement = document.getElementById('myVideoFlv')
                if (this.flvPlayer) {
                    this.flvPlayer.pause()
                    this.flvPlayer.unload()
                    this.flvPlayer.detachMediaElement()
                    this.flvPlayer.destroy()
                }
                this.flvPlayer = flvjs.createPlayer(
                {
                    type: 'flv',
                    url: this.webrtcUrl
                },
                {
                    Cors: true, // Whether cross -domain
                                         EnableWorker: true, // Whether to work in multi -threaded
                                         Anablestashbuffer: false, // Whether to enable a cache
                                         StashinitialSize: 384, // Caches (KB) default 384KB
                                         AutoCleanupsourcebuffer: true // Whether to automatically cache
                }
                )
                this.flvPlayer.attachMediaElement(videoElement)
                this.flvPlayer.load()
                this.flvPlayer.play()
                                 //
                this.flvPlayer.on(flvjs.Events.ERROR, (errType, errDetail) => {
                console.log('errType:', errType)
                console.log('errorDetail:', errDetail)
                if(this.flvPlayer){
                        this.destoryVideo()
                        this.webrtcPlay()
                    }
                })
            }
        },
        createVideo() {
            if (flvjs.isSupported()) {
                var videoElement = document.getElementById('myFlvVideo')
                this.flvPlayer = flvjs.createPlayer(                    
                    {
                    //     type: 'flv',
                    //     isLive: true,
                    //     hasAudio: false,
                         url:
                             'http://123.456.7.89:80/live?port=1935&app=myapp&stream=mystream'
                    },
                    // {
                    //     cors: true, // пересечь -домен
                    //     enableWorker: true, // работать в многочисленном
                    //     enableStashBuffer: false, // включить кеш
                    //     stashInitialSize: 128, // размер кэша (KB) по умолчанию 384 КБ
                    //     autoCleanupSourceBuffer: true // автоматически очистить кэш
                    // }
                )
                this.flvPlayer.attachMediaElement(videoElement)
                this.flvPlayer.load()
                this.flvPlayer.play()
                //
                this.flvPlayer.on(flvjs.Events.ERROR, (errType, errDetail) => {
                    console.log('errorType:', errType)
                    console.log('errorDetail:', errDetail)
                    if (this.flvPlayer) {
                        this.destoryVideo()
                        this.createVideo()
                    }
                })
            }
        },
        destoryVideo() {
            this.flvPlayer.pause()
            this.flvPlayer.unload()
            this.flvPlayer.detachMediaElement()
            this.flvPlayer.destroy()
            this.flvPlayer = null
        },
    },
    
    mounted() {
        this.$nextTick(() => {
          //  this.createVideo()
            // this.webrtcPlay()
            this.init_api()
        })
    },
    beforeDestroy() {
        this.destoryVideo()
    },
}
</script>