<?php
/**
 * Created by PhpStorm.
 * User: CKylin
 * Date: 2017/9/10
 * Time: 12:23
 */

output("XI MA LA YA Track Downloader");

//UI MODE
while (true) {
    output("\n");
    $res = ask("Enter a Track URL: ");
    if (empty($res)) {
        output("[URL not vaild] Please type a FULL URL!");
        continue;
    }
    if ($res == "exit") {
        output("\n Exited.");
        break;
    }
    $urlinfos = parse_url($res);
    $track = getTrack($urlinfos);
    if ($track === false) continue;
    $track = str_replace("/", "", $track);
    output("\nTrack ID: $track \nFetching infos...");
    $api = "http://www.ximalaya.com/tracks/$track.json";
//    $httpinfo;
//    $res = http_get($api, $httpinfo);
//    if ($httpinfo['response_code'] != "200") {
//        output("HTTP " . $httpinfo['response_code'] . " ERROR. JSON data get failed.");
//        continue;
//    }
    $r = cUrl($api);
//    $r = json_decode($res);
    if (empty($r)) {
        output("Parsing data failed.");
        continue;
    }
    if ($r['id'] == $track) {
        output("Track found:\n\n");
        $down = $r['play_path'];
        $duration = $r['duration'] / 60;
        $title = getT($r['title']);
        $user = getT($r['nickname']);
        $realtime = getT($r['formatted_created_at']);
        $time = getT($r['time_until_now']);
        $album = getT($r['album_title']);
        $intro = getT($r['intro']);
        output("USER: $user \nLENGHT: $duration min \nTITLE: $title \nALBUM: $album \nUPLOAD: $time / $realtime \nINTRO: $intro \nDOWNLOAD: $down");
        $ran = rand(00001, 99999);
//        $filename = str_replace(" ","","$user-$title-$time-$ran.m4a");
        $filename = "$user-$title-$time-$ran.m4a";
        output("\n\nDownloading...($filename)");
        $path = str_replace("\\","\\\\",dirname(__FILE__)) . "\\\\audios\\\\";
        $filepath = $path . $filename;
        @mkdir($path);
        output("Output folder: $path");
        $target = fopen($down, "rb");
        $newfile = '';
        if ($target) {
            $newfile = fopen($filepath, "wb");
            if ($newfile) {
                output("Downloading...");
                while (!feof($target)) {
                    fwrite($newfile, fread($target, 1024 * 8), 1024 * 8);
                }
                output("File block got. Closing exist streams...");
            } else {
                //fclose($newfile);
                output("File writing error. Can't open local file. Please check the permissions.($filepath)");
                fclose($target);
                continue;
            }
        } else {
            //fclose($target);
            output("File getting error. Can't open remote file. Please check the network.($down)");
        }
        if ($target) fclose($target);
        if ($newfile) fclose($newfile);
        output("\n\nFile success downloaded to $filepath\n\n");
        continue;
    } else {
        output("Parsing data error.");
        continue;
    }
}

function output($out)
{
    fwrite(STDOUT, "\n$out\n");
}

function ask($out)
{
    output($out);
    return trim(fgets(STDIN));
}

function getTrack($info)
{
    if (empty($info)) return false;
    if ($info["host"] != "www.ximalaya.com") {
        output("[URL not vaild] Please type a FULL URL!");
        return false;
    }
    $sound = stristr($info["path"], "/sound/");
    if ($sound === false) {
        output("[URL not vaild] Please type a TRACK URL!");
        return false;
    }
    $sound = str_replace("/sound/", "", $sound);
    return $sound;
}

function cUrl($url, $header = null, $data = null)
{
    //初始化curl
    $curl = curl_init();
    //设置cURL传输选项

    if (is_array($header)) {

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);


    if (!empty($data)) {//post方式
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    //获取采集结果
    $output = curl_exec($curl);

    //关闭cURL链接
    curl_close($curl);

    //解析json
    $json = json_decode($output, true);
    //判断json还是xml
    if ($json) {
        return $json;
    } else {
        #验证xml
        libxml_disable_entity_loader(true);
        #解析xml
        $xml = simplexml_load_string($output, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $xml;
    }
}
function getT($t){
//    return $t;
    return iconv("UTF-8","GBK",$t);
}

output("\n\nPHP Scripts Stopped.\n\n");