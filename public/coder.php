<?php

 // Получаем необходимые параметры
$name = $_GET['name'] ?? '1.mp4';
$forced = $_GET['forced'] ?? 0;
$fileName = getFileName($name);

$outPath = '/Users/song/video';
$inPath = '/root/download';
$dir = __DIR__;

 // определить, был ли код перекодирован ранее, и вернуть его, если он не был принудительным
if (is_dir("$outPath/$fileName") && empty($forced)) {
    header("location:./static/{$fileName}/index.m3u8");
    die;
}

 // Нанести на карту цель
system("ln -s {$outPath}  {$dir}/static");

 // Сначала создаем папку
system("mkdir -p {$outPath}/{$fileName}");
var_dump($inPath .'/' .$name );
 // транскодировать
$ffmpeg = "docker run -v $outPath:/root/download  jrottenberg/ffmpeg:latest";
$cmd = "nohup $ffmpeg -i {$inPath}/{$name}  -hls_time 10 -hls_list_size 0 -f hls -r 25 {$inPath}/{$fileName}/index.m3u8 >> ./code.log &";
system($cmd);
var_dump($cmd);


 // Задержка выполнения прыжка
returnUrl($fileName);

function getFileName($filename)
{
    $houzhui = substr(strrchr($filename, '.'), 1);
    $result = basename($filename, "." . $houzhui);

    return $result;

}

function returnUrl($fileName)
{
         echo "<a class='btn btn-video btn-lg' href='./static/ndom$fileName отпуска/index.m3u8'> <h1> Обработка ... нажмите, чтобы перейти </ h1> < / a> ";
    die;
}
