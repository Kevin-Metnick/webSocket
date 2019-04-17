<?php
namespace app;

/**
 *文件上传
 * @author  kevin-metnick
 */
class Upload
{
    static private $uplodDir = __DIR__."/../upload/";

    /**
     * 创建目录
     * @return bool
     */
    static private function isDir()
    {
        if(is_dir(self::$uplodDir))  return false;
        mkdir(self::$uplodDir);
        return true;
    }

    static function uploadFile($file)
    {
        $extorin = explode('/',$file['type'])[1];
        $name =  'F'.time().rand(1,100);
        $fileName=  $name.'.'.$extorin;
        $upload_dir = __DIR__.'/../upload';
        touch('./upload/'.$fileName);
        $tmp_dir = $upload_dir."/".$fileName;
        if(move_uploaded_file($file['tmp_name'],$tmp_dir)){
            return ['tmp_dir'=>$tmp_dir,'fileName'=>$fileName];
        }
        return false;
    }

    static function uploadImg($file)
    {
        $extorin = explode('/',$file['type'])[1];
        $name =  'F'.time().rand(1,100);
        $fileName=  $name.'.'.$extorin;
        $upload_dir = __DIR__.'/../upload';
        touch('./upload/'.$fileName);
        $tmp_dir = $upload_dir."/".$fileName;
        if(move_uploaded_file($file['tmp_name'],$tmp_dir)){
            self::thrum($tmp_dir, 300,200);
            return [
                    't_url'=>$upload_dir."/t_".$fileName,
                    'url'=>$tmp_dir,
                    'fileName'=>$name,
                    'extorin'=>$extorin,

            ];
        }
        return false;
    }


    /**
     *处理base64 文件
     */
   static public function  base64($base64Con)
   {
        $uploadDir = self::$uplodDir;
        self::isDir();
        $dir = $uploadDir.date("Ymd");
        if (!is_dir($dir)) mkdir($dir);
        $extorin = explode('/', getimagesize($base64Con)['mime'])[1]?:'jpg';
        $base64Img = base64_decode(explode(',', $base64Con)[1]);
        $time = time().rand(1,100);
        $file = $dir.'/'.$time.'.'.$extorin;
        $res = file_put_contents($file, $base64Img);

        if  ($res) return [
            'url'=>$file,
            'fileName'=>$time,
            'extorin'=>$extorin,
        ];
        return false;
   }

    /**
     *处理base64 图片
     * @param  string $base64Con
     * @return array | bool
     */
    static public function  base64Img($base64Con)
    {
        $uploadDir = self::$uplodDir;
        self::isDir();
        $dir = $uploadDir.date("Ymd");
        if (!is_dir($dir)) mkdir($dir);
        $extorin = explode('/', getimagesize($base64Con)['mime'])[1]?:'jpg';
        $base64Img = base64_decode(explode(',', $base64Con)[1]);
        $time = 'F'.time().rand(1,100);
        $file = $dir.'/'.$time.'.'.$extorin;
        $res = file_put_contents($file, $base64Img);
        if  ($res) return $file;
        self::thrum($file, 300,200);
        return [
            't_url'=>$dir.'/t_'.$time.'.'.$extorin,
            'url'=>$file,
            'fileName'=>$time,
            'extorin'=>$extorin,
        ];
    }

    /**
     * @param $src_file
     * @param $des_w
     * @param $des_h
     */
    static public function thrum($src_file, $des_w, $des_h)
    {
        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
        $srcarr = getimagesize($src_file);
        switch ($srcarr[2]) {
            case 1://gif
                $imagecreatefrom = 'imagecreatefromgif';
                $imageout        = 'imagegif';
                break;
            case 2://jpg
                $imagecreatefrom = 'imagecreatefromjpeg';
                $imageout        = 'imagejpeg';
                break;
            case 3://png
                $imagecreatefrom = 'imagecreatefrompng';
                $imageout        = 'imagepng';
                break;
        }

        $src_img = $imagecreatefrom($src_file);
        $src_w = imagesx($src_img);
        $src_h = imagesy($src_img);
        $scale = ($src_w / $des_w) > ($src_h / $des_h) ? ($src_w / $des_w) : ($src_h / $des_h);
        $des_w = floor($src_w / $scale);
        $des_h = floor($src_h / $scale);
        $des_img = imagecreatetruecolor($des_w, $des_h);
        $des_x = 0;
        $des_y = 0;
        $src_x = 0;
        $src_y = 0;
        imagecopyresampled($des_img, $src_img, $des_x, $des_y, $src_x, $src_y, $des_w, $des_h, $src_w, $src_h);
        $t_file = basename($src_file);
        $t_dir = dirname($src_file);
        $s_file = $t_dir . '/' . 't_' . $t_file;
        $imageout($des_img, $s_file);
    }

}