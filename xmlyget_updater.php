<?php
/**
 * Created by PhpStorm.
 * User: CKylin
 * Date: 2018/6/2
 * Time: 19:22
 */
error_reporting(false);

$copyright = "
 -------------------------------------------------
>                XMLYGET  UPDATER                 <
>                AUTHOR: CKYLINMC                 <
>                OPENSOURCE:GPLv3                 <
> PROJECT: https://github.com/Cansll/XiMaLaYa-Get <
 -------------------------------------------------
";
$logo = " _____ _  __     _ _       __  __  _____ 
/ ____| |/ /    | (_)     |  \/  |/ ____|
| |    | ' /_   _| |_ _ __ | \  / | |     
| |    |  <| | | | | | '_ \| |\/| | |     
| |____| . \ |_| | | | | | | |  | | |____ 
\_____|_|\_\__, |_|_|_| |_|_|  |_|\_____|
            __/ |                        
           |___/                         ";
output($copyright."\n\n".$logo);

sleep(1);
update();

function update(){
    $updater = dirname(__FILE__).DIRECTORY_SEPARATOR."xmlyget_updater.php";
    $new = dirname(__FILE__).DIRECTORY_SEPARATOR."xmlyget_new.php";
    $old = dirname(__FILE__).DIRECTORY_SEPARATOR."xmlyget.php";
    $bak = dirname(__FILE__).DIRECTORY_SEPARATOR."xmlyget.php.bak";
    if(!file_exists($new)) {
        output("[x] 缺少文件，无法进行更新。");
        sleep(1);
        exit();
    }
    output("[*] 正在备份当前程序文件...");
    if(rename($old,$bak)){
        output("[*] 正在更新程序文件...");
        if(rename($new,$old)){
            output("[*] 更新成功...");
            @system("del /F /Q ".$updater);
            @system("del /F /Q ".$bak);
            @system("@start run32.bat");
            sleep(1);
            exit();
        }else{
            output("[x] 程序文件添加失败，正在恢复备份...");
            if(rename($bak,$old)){
                output("[*] 已恢复");
            }else{
                output("[*] 恢复失败，建议重新下载程序文件。");
                output("[*] 项目地址：https://github.com/Cansll/XiMaLaYa-GET/releases");
            }
        }
    }else{
        output("[x] 备份时出错。更新失败。");
    }
    sleep(5);
    exit();
}




function output($out)
{
    $out = t($out);
    fwrite(STDOUT, "\n$out");
}

function raw_output($out)
{
    // $out = t($out);
    fwrite(STDOUT, "\n$out");
}

function ask($out)
{
    output($out);
    return trim(fgets(STDIN));
}
function t($t){
//    return $t;
    return iconv("UTF-8","GBK",$t);
}
