<?php
if (!function_exists('system')){
       die('system() not enable');
}
//c:\wamp\bin\php\php5.2.6\php-cgi.exe pack.php
set_time_limit(0);

$file_arr = array(
'2'
);
//$file_arr = array('walk');
define( 'PATH_ROOT', dirname(__FILE__).'/' );


$input_path1  =  PATH_ROOT . 'input/';
$output_path1  = PATH_ROOT . 'output/';

//echo $input_path1;

//echo $output_path1;


function scan_Dir1($dir) {
    $arrfiles = array();
    if (is_dir($dir)) {
        if ($handle = opendir($dir)) {
            chdir($dir);
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($file)) {

                        $arrfiles[] = $file;
                    }
                }
            }
            chdir("../");
        }
        closedir($handle);
    }
    return $arrfiles;
}


$arr = scan_Dir1($input_path1);
$arrfiles = array();
foreach ($arr as $value) {
    $arrfiles[] = $value;

    //echo "\n";
	//echo $value;
	//echo "\n";
}

$file_arr = $arrfiles;

foreach ($file_arr as $output_file) {
    //$output_file = 'toilet';
    $input_path  = $input_path1 . $output_file;
    $output_path = $output_path1 . $output_file;

    $str = "TexturePacker --max-width 2048 --max-height 2048 --pack-mode Best --size-constraints AnySize --data $output_path/$output_file.xml --format sparrow --sheet $output_path/$output_file.png $input_path";

//echo $str;

    $cmd = $str;
    system($cmd);
}




?>