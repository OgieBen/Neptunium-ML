<?php
<?php

//$image = imagecreatefromjpeg(__DIR__.'/A1.png'); // imagecreatefromjpeg/png/

namespace DataProcessor;

use ErrorException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use RecursiveRegexIterator;

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
        'xpm' => 'imagecreatefromxpm'
    ];
    private static $TYPE_ERR = "Invalid Type::Please use either jpeg,png,gif or maybe gd, gd2, webp file";
    private static $FILE_NOT_FOUND = "File was not found on the specified part: ";

    function __construct() {
        // imageconvolution($image, $matrix, $div, $offset)
        // imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
        // imagecreatefromgd($filename)
    }

    public function imageData($imagePath, $bool = false, $altType = null) {
        //'../Tests/A1.png'
        
        $imageDetail = explode('.', $imagePath);

        if (file_exists($imagePath)) {


            /* checks if type is jpeg or 
             * any other common type.
             * This -if-statement is not 
             * really necessary
             */

            if (!$bool) {
                $type = strtolower($imageDetail[(count($imageDetail)) - 1]);
                if (array_key_exists($type, $this->types)) {

                    $image = $this->types[$type]($imagePath);
                    // echo "++++++".$this->types[$type];
                } else {
                    //   echo "-----".$this->types[$type]($imagePath);
                    throw new \ErrorException(self::$TYPE_ERR);
                }
                //if type is someother type
            } else {

                if (array_key_exists($type = $altType, $this->types)) {
                    $image = $this->types[$type]($imagePath);
                } else {
                    throw new \ErrorException(self::$TYPE_ERR);
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
                    //channels
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
        } else {
            throw new Exception(self::$FILE_NOT_FOUND . '' . $imagePath);
        }
    }

    public static function subWindows(Array $imageData, int $widthSize = 20, int $heightSize = 20, $stride = 1) {

        /*
         * This function outputs row first
         * it doesnt use the conventionl matlab 
         * style. so watch out!
         * ---filters should be stacked in row
         * first order.
         *  e.g [1,2,3,4]
         *      1 and 2 are the first elements of the filter 
         *      in the first row while 3 and 4 are the 
         *      first element at row 2 in a 2x2 filter
         *      same applies to subwindows.
         *       
         * 
         */
        $subWindow = array();
        $temp = array();
        $topLevel = 0; //using zero indexing to navigate through the array
        /*
         * TODO: check if the imageData is a two dimesional array
         * transform to two dimensional if not
         * assumes $imageData to be two dimensional
         * 
         */

        $maxColStride = self::numberOfStrides(1, $heightSize, count($imageData));

        for ($row = 0; $row < $maxColStride; $row++) {
            //get sub sections
            array_push($subWindow, self::fetch($imageData, $widthSize, $heightSize));
            // remove from top row section
            array_shift($imageData);
        }
        //returns 3-d array for easy refrence
        //this should be abstracted
        return $subWindow;
    }

    /* fetch gets different areas of an image based on a given stride
     * default is is 1
     * 
     */

    private static function fetch($subImageData, $subWidth, $subHeight, $stride = 1) {

        //gets number of stride movements
        $max = self::numberOfStrides($stride, $subWidth, count($subImageData[0]));
        $temp = array();
        $holder = array();

        //moves the stride
        for ($stridePixMov = 0; $stridePixMov < $max; $stridePixMov+=$stride) {
            for ($row = 0; $row < $subHeight; $row++) {
                for ($col = $stridePixMov; $col < ($subWidth); $col++) {
                    //  if (($col <= $max)) {
                    //creates a small structure  
                    array_push($temp, $subImageData[$row][$col]);
                    //  }
                }
            } $subWidth++;
            // echo "<br/> St " .$stridePixMov." : ";
            // print_r($temp);
            // echo "<br/>";
            array_push($holder, $temp);
            $temp = array();
        }
        return $holder;
    }

    private static function numberOfStrides($stride, $filterWidth, $maxwidthOrHeight) {
        $trck = 1;

        for ($i = 0; $i < $maxwidthOrHeight; $i++) {
            $maxwidthOrHeight -=$stride;
            if ($maxwidthOrHeight < $filterWidth) {
                return $trck;
            }
            $trck++;
        }
        return $trck;
    }

    //reduces higher dimension arrays or tensor matrices to just one dim
    //Note: unstable
    //test::upgrade
    public static function dropArrayDim($array) {
        $temp = array();

        //Todo: upgrade to tensor 
        //check if input is tensor
        //break nested blocks
        for ($row = 0; $row < count($array); $row++) {
            for ($col = 0; $col < count($array[0]); $col++) {
                foreach ($array[$col] as $var) {
                    array_push($temp, $var);
                }
            }
        }
        return $temp;
    }

    /* matrix or array must be in 2-d format
     * TODO:  import dimension validator
     * validate matrix
     */

    public static function integralImage($imageData) {

        /*
         * validate matrix here
         */

        //var_dump($imageData);
        //flows down
        $sumDown = self::sumDown($imageData);
        // var_dump($sumDown);
        //flows right
        $sumRight = self::sumRight($sumDown);

        return $sumRight;
    }

    private static function sumRight($sumDown) {

        $temp = array();
        $holder = array();
        $val = 0;
        $colIndex = 1;


        for ($row = 0; $row < count($sumDown); $row++) {
            for ($col = 0; $col < count($sumDown[0]); $col++) {
                if ($col == 0) {
                    $val = $sumDown[$row][$col];
                    array_push($temp, $val);
                } else {
                    $val += $sumDown[$row][$col];
                    array_push($temp, $val);
                }
            }
            array_push($holder, $temp);
            $val = 0;
            $temp = array();
            $colIndex = 1;
        }
        return $holder;
    }

    private static function sumDown($imageData) {
        $rowIndex = 1;
        $colIndex = 1;
        for ($row = 0; $row < (count($imageData)); $row++) {
            for ($col = 0; $col < (count($imageData[0])); $col++) {

                if ($rowIndex < count($imageData)) {
                    $imageData[$rowIndex][$col] = $imageData[$row][$col] + $imageData[$rowIndex][$col];
                }
            }
            $rowIndex++;
            $colIndex = 1;
        }
        return $imageData;
    }

    public static function simpleResize($newWidth, $newHeight, $src, $fileDes = null) {
        /*
         * proportion: test
         */


        /* if($oldHeight > $newHeight)
          {
          $oldwidth = ($newHeight / $oldHeight) * $oldwidth;
          }

          if($oldWidth > $newWidth)
          {
          $oldHeight = ($newHeight / $oldHeight) * $oldHeight;
          }

         * 
         */
        list( $oldWidth, $oldHeight) = getimagesize($src);

        $sample = imagecreatetruecolor($newWidth, $newHeight);
         $sr = imagecreatefromjpeg($src);
        if (imagecopyresampled($sample, $sr, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight)) {
            // imagecopyresized($sample, $src, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);
            imagefilter($sample, IMG_FILTER_GRAYSCALE);
            if (is_null($fileDes)) {
                imagejpeg($sample,$src);
                return true;
            }
            return imagejpeg($sample, $fileDes);
        }
        return false;
    }
    
    public function retrieveFiles($path, $pattern=null)
    {
        try{
        $cursor = new RecursiveIteratorIterator(
                                new \RecursiveDirectoryIterator($path),
                                \RecursiveIteratorIterator::SELF_FIRST
                );
        }catch(\Throwable $err)
        {
            echo" \nProblems with given Directory ";
            exit;
        }
        
        $patt = '/^.'.  str_replace('.', '\\.', $pattern).'/';
        $regExIt = new \RegexIterator($cursor, $patt);
        
        $runIterator = ($pattern) ? $regExIt : $cursor;
        foreach($runIterator as $it)
        {
            if(is_dir($it)){continue;}
            yield str_replace("\\", '/', $it);
        }
    }
 

    
}
