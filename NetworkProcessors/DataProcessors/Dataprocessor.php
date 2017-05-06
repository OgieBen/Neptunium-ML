<?php

//$image = imagecreatefromjpeg(__DIR__.'/A1.png'); // imagecreatefromjpeg/png/

namespace DataProcessor;

use ErrorException;

class DataProcessor {

    private $types = [
        'jpeg' => 'imagecreatefromjpeg',
        'jpg' => 'imagecreatefromjpeg',
        'png' => 'imagecreatefrompng',
        'gif' => 'imagecreatefromgif',
        'gd' => 'imagecreatefromgd',
        'gd2' => 'imagecreatefromgd2',
        'gd2part' => 'imagecreatefromgd2part',
        'string' => 'imagecreatefromstring',
        'wbmp' => 'imagecreatefromwbmp',
        'webp' => 'imagecreatefromwebp',
        'xbm' => 'imagecreatefromwxbm',
        'xpm' =>  'imagecreatefromxpm'
    ];
    private static $TYPE_ERR = "Invalid Type::Please use either jpeg,png,gif or maybe gd, gd2, webp file";
    private static $FILE_NOT_FOUND ="File was not found on the specified part: ";
    function __construct() {
        // imageconvolution($image, $matrix, $div, $offset)
        // imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
       // imagecreatefromgd($filename)
    }

    function imageData($imagePath, $bool = false, $altType = null) {
        //'../Tests/A1.png'
        $imageDetail = array();
        $imageDetail = explode('.', $imagePath); 
       
        if(file_exists($imagePath)){
        
      //  print_r($imageDetail);
        //use this
        if (!$bool) {
            $type = strtolower($imageDetail[(count($imageDetail)) - 1]);
          if(array_key_exists($type, $this->types)){
           
                $image = $this->types[$type]($imagePath);
               // echo "++++++".$this->types[$type];
            } else {
              //   echo "-----".$this->types[$type]($imagePath);
              throw  new \ErrorException(self::$TYPE_ERR);
            }           
        } else {
            if (array_key_exists($type = $altType, $this->types)) {
                $image = $this->types[$type]($imagePath);
            } else {
             throw   new \ErrorException(self::$TYPE_ERR);
            }
        }

        // $image = imagecreatefrompng($imagePath);
        $width = imagesx($image);
        $height = imagesy($image);
        $colors = array();
        $colorRows = array();
        for ($i = 0; $i < $height; $i++) {
            $y_array = array();

            for ($j = 0; $j < $width; $j++) {
                $rgb = imagecolorat($image, $i, $j);
                array_push($colorRows, $rgb);
                /*  $r = ($rgb >> 16) & 0xFF;
                  $g = ($rgb >> 8) & 0xFF;
                  $b = $rgb & 0xFF;

                  $x_array = array($r, $g, $b) ;
                  $y_array[] = $x_array ; */
            }
            array_push($colors, $colorRows);
            //echo "<br/><br/><br/>";
            //$colorRows=array();
        }
        // print_r($colorRows);
//print_r($colors);
         //returns single vector of data;
         return $colorRows;
         
         
        }else{
            throw new Exception(self::$FILE_NOT_FOUND.''.$imagePath);
        }
         
    }

}
