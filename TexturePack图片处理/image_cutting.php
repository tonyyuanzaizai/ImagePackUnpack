<?php
/**
 * Created by PhpStorm.
 * User: Yuheng
 * Date: 2015/1/17 0017
 * Time: 下午 17:06
 */
//header("Content-Type:image/png");
$source_pic['pump']['pic'] = __DIR__ . "/images/pump.png";
$source_pic['pump']['xml'] = __DIR__ . "/images/pump.xml";


$source_dir = isset($argv[1]) ? $argv[1] : "";
if($source_dir !== ""){
    $dir_list = scandir($source_dir);
    foreach($dir_list as $list){
        $file = $source_dir."/".$list;
        if(strpos($list,".png") !== false){
            $source_pic['pump']['pic'] = $file;
        }
        elseif(strpos($list,".xml") !== false){
            $source_pic['pump']['xml'] = $file;
        }
    }
}





$output_dir = "output";
if (!is_dir($output_dir)) {
    mkdir($output_dir);
}


function makePicture($frameWidth, $frameHeight, $frameX, $frameY, $old_pic, $x, $y, $width, $height, $filename)
{

    $new_pic = imagecreatetruecolor($frameWidth, $frameHeight);
    $background_color = imagecolorallocatealpha($new_pic, 100, 100, 100, 127);

    imagealphablending($new_pic, true);
    imagefill($new_pic, 0, 0, $background_color);

    imagecolortransparent($new_pic, $background_color);
    imagealphablending($new_pic, false);

    imagesavealpha($new_pic, true);

    $pic_x = abs($frameX);
    $pic_y = abs($frameY);
    imagecopyresized($new_pic, $old_pic, $pic_x, $pic_y, $x, $y, $width, $height, $width, $height);


    $path = pathinfo($filename);

    $dir_path = explode("/",$path['dirname']);
    $dir="";
    foreach($dir_path as $one){
        $dir .= $one."/";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
    }

    imagepng($new_pic, $filename);
    imagedestroy($new_pic);
}

foreach ($source_pic as $item) {
    $picture = $item['pic'];
    $xml_config = $item['xml'];

    list($pic_width, $pic_height) = getimagesize($picture);
    $old_pic = imagecreatefrompng($picture);
    $config = simplexml_load_file($xml_config);
    foreach ($config->SubTexture as $item) {
        $name = strval($item['name']);
        $x = $new_arr[strval($item['name'])]['x'] = strval($item['x']);
        $y = $new_arr[strval($item['name'])]['y'] = strval($item['y']);
        $width = $new_arr[strval($item['name'])]['width'] = strval($item['width']);
        $height = $new_arr[strval($item['name'])]['height'] = strval($item['height']);
        $frameWidth = $new_arr[strval($item['name'])]['frameWidth'] = strval($item['frameWidth']);
        $frameHeight = $new_arr[strval($item['name'])]['frameHeight'] = strval($item['frameHeight']);
        $frameX = $new_arr[strval($item['name'])]['frameX'] = strval($item['frameX']);
        $frameY = $new_arr[strval($item['name'])]['frameY'] = strval($item['frameY']);

        $filename1 = "$output_dir/1/$name.png";
        $filename2 = "$output_dir/2/$name.png";
        makePicture($frameWidth, $frameHeight, $frameX, $frameY, $old_pic, $x, $y, $width, $height, $filename1);
        makePicture($width, $height, 0, 0, $old_pic, $x, $y, $width, $height, $filename2);
    }
}

//$complete_png = imagecopyresized($new_pic,$old_pic,0,0,0,0,$new_width,$new_height,$pic_width,$pic_height);
//imagepng($new_pic);
