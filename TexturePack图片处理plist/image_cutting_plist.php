<?php
/**
 * Created by PhpStorm.
 * User: Yuanyuan
 * Date: 2018/1/23 0017
 * Time: 下午 17:06
 */
//header("Content-Type:image/png");
$source_pic['pump']['pic'] = __DIR__ . "/images/gamepage0.png";
$source_pic['pump']['xml'] = __DIR__ . "/images/gamepage0.plist";

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


//fix value node
$f = $source_pic['pump']['xml'];
file_put_contents($f,str_replace('<true/>', '<string>true</string>', file_get_contents($f))); 
file_put_contents($f,str_replace('<false/>','<string>false</string>', file_get_contents($f))); 



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

    $imgDict = $config->dict->dict;
    $imageNameArray = array();
    foreach ($imgDict->key as $a=>$imgKey) {
        $imgName = (string)$imgKey;
        $imageNameArray[] = $imgName;
    }
    $imagePropertyArray = array();
    foreach ($imgDict->dict as $a=>$dictKey) {
        var_dump($dictKey->asXML()  );
        $imageProperty = array();
        foreach ($dictKey as $dictKey => $dictVal) {
            $dictV = (string)$dictVal;
            $imageProperty[] = $dictV;
        }
        $imagePropertyArray[] = $imageProperty;
    }

    foreach ($imageNameArray as $i=>$imageName) {
        $name = strval($imageName);
        $imageProperty = $imagePropertyArray[$i];
        
        /*
        array(10) {
  [0]=>
  string(5) "frame"
  [1]=>
  string(19) "{{354,124},{20,22}}"
  [2]=>
  string(6) "offset"
  [3]=>
  string(5) "{0,0}"
  [4]=>
  string(7) "rotated"
  [5]=>
  string(4) "true"
  [6]=>
  string(15) "sourceColorRect"
  [7]=>
  string(15) "{{0,0},{20,22}}" 
  [8]=>
  string(10) "sourceSize"// 原始大小
  [9]=>
  string(7) "{20,22}"
}


<dict>
                <key>frame</key>
                <string>{{288,416},{91,87}}</string> frameX，frameY, frameWidth, frameHeight
                <key>offset</key>
                <string>{3,-1}</string> offsetX offsetY
                <key>rotated</key>
                <true/>
                <key>sourceColorRect</key>
                <string>{{13,13},{91,87}}</string> x-offsetX,y-offsetY,w,h
                <key>sourceSize</key>
                <string>{111,111}</string>// 原始大小
            </dict>
            
        */
        //var_dump($imageProperty);
        
        $frameArr = getSpecArr($imageProperty[1]);
        $offsetArr = getSpecArr($imageProperty[3]);
        $rotated = $imageProperty[5];
        $sourceColorRectArr = getSpecArr($imageProperty[7]);
        $sourceSizeArr = getSpecArr($imageProperty[9]);
        
        $x = $new_arr[$imageName]['x'] = intval($sourceColorRectArr[0]) - intval($offsetArr[0]);//strval($item['x']);
        $y = $new_arr[$imageName]['y'] = $sourceColorRectArr[1] - $offsetArr[1];//strval($item['y']);
        $width = $new_arr[$imageName]['width'] = $sourceSizeArr[0];//strval($item['width']);
        $height = $new_arr[$imageName]['height'] = $sourceSizeArr[1];//strval($item['height']);
        $frameWidth = $new_arr[$imageName]['frameWidth'] = $frameArr[2];//strval($item['frameWidth']);
        $frameHeight = $new_arr[$imageName]['frameHeight'] = $frameArr[3];//strval($item['frameHeight']);
        $frameX = $new_arr[$imageName]['frameX'] = $frameArr[0];//strval($item['frameX']);
        $frameY = $new_arr[$imageName]['frameY'] = $frameArr[1];//strval($item['frameY']);

        
        if($width < 1 || $height < 1 || $frameWidth < 1 || $frameHeight < 1 ){
            continue;
        }
        $filename1 = "$output_dir/1/$name.png";
        $filename2 = "$output_dir/2/$name.png";
        makePicture($frameWidth, $frameHeight, $frameX, $frameY, $old_pic, $x, $y, $width, $height, $filename1);
        makePicture($width, $height, 0, 0, $old_pic, $x, $y, $width, $height, $filename2);
    }
}

function getSpecArr($str){
    //$str = '{{13,13},{91,87}}';
    $str = str_replace('{','', $str); 
    $str = str_replace('}','', $str); 

    $arr = explode(',', $str);
    //var_dump($arr);
    return $arr;

}

/**
 * @param SimpleXMLElement $xmls
 * @return array
 */
function parseXml($xmls)
{
    $array = [];
 
    foreach ($xmls as $key => $xml) {
        /** @var SimpleXMLElement $xml */
        $count = $xml->count();
 
        if ($count == 0) {
            $res = (string) $xml;
        } else {
            $res = parseXml($xml);
        }
 
        $array[$key] = $res;
    }
 
    return $array;
}


//$complete_png = imagecopyresized($new_pic,$old_pic,0,0,0,0,$new_width,$new_height,$pic_width,$pic_height);
//imagepng($new_pic);
