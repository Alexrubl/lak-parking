
[Unit]
Description=My ffmpeg Service

[Service]
Type=simple
ExecStart=ffmpeg -i rtsp://user:password12345678@192.168.8.198/Streaming/Channels/102 -fflags flush_packets -max_delay 3 -flags -global_header -hls_time 2 -hls_list_size 4 -hls_flags delete_segments -vcodec copy -y /home/multidat/lak/public/stream/camera4/file.m3u8
Restart=always

[Install]
WantedBy=multi-user.target



192.168.0.94
ffmpeg -i rtsp://admin:admin123@domdeneg.keenetic.pro:9554/Streaming/Channels/102 -fflags flush_packets -max_delay 3 -flags -global_header -hls_time 2 -hls_list_size 4 -hls_flags delete_segments -vcodec copy -y /home/alexrubl/lak/public/stream/camera1/test.m3u8


ffmpeg -i rtsp://test:123456789qQ@5.165.25.145:55554/Streaming/Channels/101 -fflags flush_packets -max_delay 5 -flags -global_header -hls_time 5 -hls_list_size 3 -hls_flags delete_segments -vcodec copy -y /home/alexrubl/lak/public/stream/camera1/file.m3u8


ffmpeg -i rtsp://admin:1932865!camera@192.168.0.94 -fflags flush_packets -max_delay 3 -flags -global_header -hls_time 2 -hls_list_size 4 -hls_flags delete_segments -vcodec copy -y /home/alexrubl/lak/public/stream/camera1/test.m3u8

ffmpeg -v info -i rtsp://admin:admin123@domdeneg.keenetic.pro:9554 -c:v copy -c:a copy -bufsize 1835k -pix_fmt yuv420p -flags -global_header -hls_time 10 -hls_list_size 6 -hls_wrap 10 -start_number 1 /home/alexrubl/lak/public/stream/camera1/video.m3u8


ffmpeg -i rtsp://admin:admin123@domdeneg.keenetic.pro:9554 -c copy -f hls -hls_list_size 50000 -hls_time 5 -hls_flags delete_segments -reset_timestamps 1 /home/alexrubl/camera/video.m3u8

ffmpeg -i rtsp://admin:admin123@domdeneg.keenetic.pro:9554/Streaming/Channels/102 -c copy -f hls -hls_list_size 50000 -hls_time 5 -hls_flags delete_segments -reset_timestamps 1 c:/1/video.m3u8

ffmpeg -i rtsp://admin:admin123@domdeneg.keenetic.pro:9554/Streaming/Channels/102 -fflags flush_packets -max_delay 3 -flags -global_header -hls_time 2 -hls_list_size 4 -hls_flags delete_segments -vcodec copy -y c:/1/video.m3u8


ffmpeg -i rtsp://user:password12345@212.23.66.165:55011/Streaming/Channels/101 -fflags flush_packets -max_delay 5 -flags -global_header -hls_time 5 -hls_list_size 3 -hls_flags delete_segments -vcodec copy -y /home/alexrubl/lak/public/stream/camera1/file.m3u8

ffmpeg -i rtsp://user:password12345@212.23.66.165:55011/Streaming/Channels/101 -fflags flush_packets -max_delay 3 -flags -global_header -hls_time 2 -hls_list_size 4 -hls_flags delete_segments -vcodec copy -y c:/1/video.m3u8


rtmp://localhost:1935/app/aKq#1kj/Stream
http://localhost:8080/app/aKq#1kj/Stream
