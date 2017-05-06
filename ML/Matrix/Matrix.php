<?php

namespace Matrix;

use ErrorException;

class Matrix {

    private $array;
    private static $dim = null;
    private static $dimCol = null;
    private static $DIM_ERR = "No Matrix given ";
    private static $IRREGULAR_MATRIX_ERR = 'Iregular Matrix size. Please check row ';
    private static $level = 0;
    private static $holder = array();
    private static $detSumArray = array();
    private static $mat2 = array();

    public function __construct() {
        
    }

    public static function multiply(Array $array1, Array $array2) {
      
        list($dim1, $dim2) = self::getDimension($array1);
       
        list($dim3, $dim4) = self::getDimension($array2);
       
        $results = array();
        $vecSum = 0;
        //use number of rows as dim values to get columns
        self::$dim = $dim1;
        self::$dimCol = $dim3;
       
        if (($dim2) == $dim4) {
            echo "<br/>Resulting array : " . $dim1 . ' x ' . $dim3;
            $index = 0;
            $val = 0;

            for ($lengthOfMat1 = 0; $lengthOfMat1 < count($array1); $lengthOfMat1++) {
                for ($lengthOfMat2 = 0; $lengthOfMat2 < count($array2); $lengthOfMat2++) {
                    for ($it = 0; $it < count($array2[$lengthOfMat2]); $it++) {
                        $val+=$array1[$lengthOfMat1][$it] * $array2[$lengthOfMat2][$it];
                    }array_push($results, ($val));
                    $val = 0;
                }
            }
            echo "<br/><br/>***Mutiply outputs: ";
            print_r($results);
            return $results;
        } else {
            echo "<br/>Error: Matrices are of different length "
            . "<br/>Matrix one: Of Dim " . $dim1 . ' x ' . $dim2
            . "<br/> Matrix two: Of Dim " . $dim3 . ' x ' . $dim4;


            echo "<br/><br/>Notice: Please if you are trying to multiply a single vector with a multi-dimensional Matrix, "
            . "<br/>make sure the vector is in a two dimensional array format<br/> e.g:<br/> \$array1=  array( "
            . "[0,1,1,3] );";

            return false;
        }
    }

    public static function getDimension(Array $matrix) {
        //  echo "<br/>Note: rows are always read as columns(or vectors) when used as single array or row matrix <br/> ";
        $tracker = 0;
        //modify this line of code to run through all rows in array/matrix
        $innerLength = count($matrix[0]);
        for ($i = 0; $i < count($matrix); $i++) {
            if ($innerLength != count($matrix[$i])) {
                throw new \Exception(self::$IRREGULAR_MATRIX_ERR . ++$i . ' in the given matrix');
            }
        }

        list($bool, $val) = self::checkMatrixDimension($matrix);
        if ($bool && $val > 0) {

            //check the lenght of each columns in the matrix
            foreach ($matrix as $value) {

                if ($innerLength != count($value)) {
                    echo "<br/> Array not having same length at row " . $tracker;
                    return array(0, 0);
                } $tracker++;
            }
            // echo"<br/> Dimension: " . count($matrix) . " x " . $innerLength;
            return array(count($matrix), $innerLength);
        }

        if ($bool && $val == 0) {
            //if its not multi-dimensional then return the length  
            //echo "<br/>Dimension " . count($matrix) . " x " . 1;
            return array(count($matrix), 1);
        }
    }

    public static function checkMatrixDimension(Array $Matrix) {
        $tracker = 0;
        foreach ($Matrix as $value) {
            if (is_array($value)) {
                $tracker++;
            }
        }

        if ($tracker < count($Matrix) && $tracker != 0) {
            echo "<br/>Error Irregular Matrix ";
            return array(false, -1);
        } elseif ($tracker == 0) {
            // echo "<br/> Matrix is a vector ";
            return array(true, 0);
        } elseif ($tracker == count($Matrix)) {
            // echo "<br/> Matrix is multi-dimensional ";
            return array(true, $tracker);
        }
    }

    public function log() {
        echo 'ben';
    }

    static function transformToRow(Array $result, int $dim) {
        $temp = array();
        $numOfStops = count($result) / $dim;
        // echo $numOfStops;
        if ($numOfStops > 0) {
            for ($i = 0; $i < $numOfStops; $i++) {
                //   echo "here";
                list($split, $arr) = self::split($result, $dim);
                $result = $arr;
                array_push($temp, $split);
            }echo "<br/>";
            // print_r($temp);
            return $temp;
        }
        return false;
    }

    static function split(Array $array, int $index) {
        $temp = array();
        for ($i = 0; $i < $index; $i++) {
            array_push($temp, array_shift($array));
        }
        print_r($temp);
        //        print_r($array);
        return array($temp, $array);
    }

    static function transpose(Array $matrix) {
        //check if columns are of equal lengths before proceeding with flow
        $temp = array();
        if (is_array($matrix[0])) {
            for ($i = 0; $i < count($matrix[0]); $i++) {//runs thru the columns assuming the are all equal
                array_push($temp, self::stripColumn($matrix, $i)); //first row now becomes column one
            }
            print_r($temp);
            return $temp;
        }

        echo 'Only multi-dimensional arrays are allowed here';
        return false;
    }

    static function getDim() {

        if (is_numeric((self::$dim))) {
            return self::$dim;
        }

        throw new \Exception(self::$DIM_ERR . ' --Description-- The value ' . self::$dim . ' was given --');
    }

    static function getDimCol() {

        if (is_numeric((self::$dimCol))) {
            return self::$dimCol;
        }

        throw new \Exception(self::$DIM_ERR . ' --Description-- The value ' . self::$dimCol . ' was given --');
    }

    //use to group results row wise for scalar multiplication
    static function stripColumn(Array $array, $column) {
        $temp = array();
        //strips column values from the matrix into a single vector
        for ($i = 0; $i < count($array); $i++) {//runs to the end of the matrix
            array_push($temp, $array[$i][$column]);
        }
        return $temp;
    }

    //used to group results column wise for scalar multiplication
    //                                array or matrix , number of rows
    static function transformToColumn(Array $array) {
        //the real transpose
        $arrayHolder = array();
        $temp = array();
        $tracker = 0;
        $index = 0;
        //echo count($array);
        list($bool, $fig) = self::getDimension($array);

        if ($bool && $fig > 0) {
            $trace = count($array[0]);
            // echo $trace;
        } else {
            return;
            $trace = count($array);
        }

        if ($trace) {
            $numberOfRows = count($array);

            //move to individual columns one at a time until it
            //gets to 'n' columns: 'n' columns represents the last column 
            //which is represented by $numberOfColumns

            for ($ind = 0; $ind < count($array[0]); $ind++) {
                $index = $tracker;

                for ($ind2 = 0; $ind2 < $numberOfRows; $ind2++) {
                    $temp[$ind2] = $array[$ind2][$ind];
                    $index += $numberOfRows;
                    //echo"<br/> ".$array[$ind2][$ind];// print_r($temp[$ind2]);
                }

                array_push($arrayHolder, $temp);
                $temp = null;
                $tracker++;
            }echo"<br/>";
            //  print_r($arrayHolder);
            return ($arrayHolder);
        }

        echo "<br/> Error : " . __METHOD__ . " Could number could not create equal numbers of elements in columns ";
    }

    public static function displayMatrix($array, $dim = null) {
        $tracker = 0;
        echo "<br/>::Display Output::";
        echo "<br/>{<br/>";
        for ($i = 0; $i < count($array); $i++) {         
            if ($dim == null) {
                list($bool, $fig) = self::getDimension($array);
                if ($bool && $fig > 0) {  
                    $dim = count($array[0]);
                } else {
                    $dim = 1;
                }
            }
            if (is_array($array[$i])) {
                for ($j = 0; $j < count($array[$i]); $j++) {

                    if (is_array($array[$i][$j])) {
                        for ($k = 0; $k < count($array[$i][$j]); $k++) {

                            if (is_int($dim) && $tracker % $dim == 0 && $tracker != 0) {
                                echo"|<br/> ";
                                printf("   %20s  ", "    ");
                            }                    
                            printf("   |%20s |", $array[$i][$j]);                        
                        }
                    } else {
                        if (is_int($dim) && $tracker != 0 && $tracker % $dim == 0) {
                            echo"<br/> ";
                            printf("   %10s  ", "     ");
                        }
                        // echo '   | ' . $array[$i][$j] . '  ';
                        printf("|%20s |", $array[$i][$j]);
                        //echo"<br/>";
                        $tracker++;
                    }
                }
            } else {
                //print per row
                
                printf("|%20s |", $array[$i]);
            }
        }echo " <br/>}<br/>";
    }

    public static function det($matrix) {
        $temp = $matrix;
        $val = 0;
        //special characters
        $rte = array();
        $mgk = array();
        $retrieve = array();
        $tracker2 = 1;
        $numTemp2 = 0;


        $holder = array();
        $tracker = 1;
        $numTemp = 0;
        // self::$level =0;
        ++self::$level;
        list($row, $col) = self::getDimension($matrix);
        echo '<br/>Layer ----' . $row . '-x- ' . $col;
        if (($row > 2) && ($col > 2) && $row == $col) {

            for ($i = 0; $i < count($matrix[0]); $i++) {
                if (count($temp[0]) > 3) {
                    echo "<h1>Level " . self::$level . " </h1>";
                    //if matrix is multidimensiona recurse through the
                    //matrix system      
                    $retrieve = self::transformToColumn(self::detStrip($matrix, $i));
                    //print_r($retrieve);
                    $det = self::det($retrieve);
                    //echo" test";
                    if ($tracker2 % 2 == 0) {
                        $numTemp2 = (-$temp[0][$i] * $det);
                    } else {
                        $numTemp2 = ($temp[0][$i] * $det);
                    } array_push($holder, $numTemp2);
                } else {
                    if (count($temp[0]) > 2) {
                        $rte = self::detStrip($matrix, $i);
                    } else {
                        $rte = $matrix;
                    }
                    //print_r($rte);
                    $val = ($rte[0][0] * $rte[1][1]) - ($rte[0][1] * $rte[1][0]);
                    $numTemp = $val;
                    //  echo"<br/> Temp " . $numTemp.'<br/>';
                    array_push(self::$mat2, $numTemp);

                    if ($tracker % 2 == 0) {
                        $val = (-$temp[0][$i] * $val);
                    } else {
                        $val = ($temp[0][$i] * $val);
                    }// echo $val;
                    array_push($holder, $val);
                    $tracker++;
                    //print_r(self::transformToColumn($rte, 2));
                    // return sum;
                }
            }

            array_push(self::$holder, $holder);
            $sum = array_sum($holder);


            array_push(self::$detSumArray, $sum);

            /* echo"<br/>Two by Two<br/>";
              print_r(self::$mat2); */
            echo "<br/><br/>Determinant Matrix: ";
            print_r($holder);
            echo "<br/>Determinant: " . $sum . '<br/>';

            return $sum;
        } elseif (($row == 2) && ($col == 2) && $row == $col) {
            $rte = $matrix;
            $val = ($rte[0][0] * $rte[1][1]) - ($rte[0][1] * $rte[1][0]);
            $numTemp = $val;
            echo "<br/>Determinant: " . $numTemp . '<br/>';
            return $numTemp;
        } else {
            echo "Matrix must be a square Matrix. Please review your matrix";
            throw new \ErrorException('Matrix must be a square Matrix. Please review your matrix');
        }
    }

    static function detStrip($matrix, $i) {
        //retrurns selected columns in row format
        //i.e row 1 holds values for columns 1 in the matrix



        $arrayHolder = array();
        $temp = $matrix;
        $k = 0;
        array_shift($temp);
        while ($k < count($matrix)) {
            if ($k != $i) {
                array_push($arrayHolder, self::stripColumn($temp, $k));
            }
            $k++;
        } // array_shift($va);
        // print_r($arrayHolder);



        return $arrayHolder;
    }

    public function adjoint() {
        //swap rows then calculate adjoint
    }

    public function inverse($matrix) {

        $mat = array();
        $k = 0;
        $detH = array();

        list($row, $col) = self::getDimension($matrix);

        self::$detSumArray = array();
        self::$mat2 = array();
        $fol = 0;
        $lof = 0;

        if ($row > 2 && $col > 2 && $col == $row) {
            $det = self::det($matrix);
            if ($det == 0) {
                echo "Matrix deos not have an inverse";
                return false;
            }
            //minor for M X M matrices 
            $detH = self::getMinors($matrix);
            $tRow = self::transformToRow($detH, $row);
            $tRow= self::getSign($tRow);
            $tCol = self::transformToColumn($tRow);
            for ($i = 0; $i < count($tCol); $i++) {
                for ($j = 0; $j < count($tCol[$i]); $j++) {

                    $tCol[$i][$j] = (($tCol[$i][$j] / $det));
                }
            }
            echo "::Inverse::";
            self::displayMatrix($tCol, $row);
            return $tCol;
        } elseif ($row == 2 && $col == 2 && $col == $row) {

            $inv = self::inv2by2($matrix);
            echo "::Inverse::";
            self::displayMatrix($inv, $row);
            return $inv;
        }
    }

    public static function iterativePushToBack($matrix) {
        /*iteratively moves each row to back until every 
         row has be tranversed*/
        $popVal = array_shift($matrix);
        array_push($matrix, $popVal);
        return $matrix;
    }

    public function inv() {
        
    }

    public static function pickOutRow($matrix, $row) {
        //picks out row from a multidimensional array 
        $temp = array();
        $f = 1;
        //  array_slice($matrix, 1, 2);
        for ($i = 0; $i < count($matrix); $i++) {
            if ($i != $row) {
                array_push($temp, $matrix[$i]);
            }
            $f++;
        }
        return $temp;
    }

    public static function pickOutColumn($array, $ind) {
        $temp = array();
        $retrieve = array();
        //picks out a particular column from a matrix then it
        // returns the matrix without the column
        for ($i = 0; $i < count($array); $i++) {//runs to the end of the matrix
            for ($j = 0; $j < count($array[$i]); $j++) {
                if ($j != $ind) {
                    array_push($temp, $array[$i][$j]);
                }
            }
            array_push($retrieve, $temp);
            $temp = array();
        }
        return $retrieve;
    }

    public static function getMinors($matrix) {
        $detH = array();
        for ($rowM = 0; $rowM < count($matrix); $rowM++) {
            // slice out the required portion 
            //  $strip = self::detStrip($matrix, $i);
            for ($colM = 0; $colM < count($matrix[$rowM]); $colM++) {
                //perform determinant operation 
                echo"<br/>";
                self::displayMatrix($matrix, count($matrix[$rowM]));
                echo"<br/>";
                $mtx = self::pickOutRow($matrix, $rowM);
                echo"<br/>";
                self::displayMatrix($mtx, count($matrix[$rowM])-1);
                echo"<br/>";
                $mtx = self::pickOutColumn($mtx, $colM);
                self::displayMatrix($mtx, count($matrix[$colM])-1);
                //array of minors
                array_push($detH, self::det($mtx));
                // $matrix = self::turnOver($matrix);
            }
        }

        return $detH;
    }

    public static function inv2by2($matrix) {
        $detH = null;
        $detH = self::det($matrix);
        $tp = $matrix[0][0];
        $matrix[0][0] = $matrix[1][1];
        $matrix[1][1] = $tp;
        $matrix[0][1] = (-1 * $matrix[0][1]);
        $matrix[1][0] = (-1 * $matrix[1][0]);

        for ($k = 0; $k < count($matrix); $k++) {
            for ($l = 0; $l < count($matrix[$k]); $l++) {
                if ($detH == 0) {
                    echo "Matrix does not have an Inverse";
                    return false;
                }
                $matrix[$k][$l] = ($matrix[$k][$l] / $detH);
            }
        }

        return $matrix;
    }
    
    
    public static function getSign($tRow)
    {
          for ($r = 0; $r < count($tRow); $r++) {
                for ($c = 0; $c < count($tRow[$r]); $c++) {
                   $fol =$r+1;
                   $lof =$c+1; $trk = ($fol + $lof);
                    $tRow[$r][$c] = (pow((-1),$trk) *($tRow[$r][$c]));
                }
            }
            return $tRow;
    }

}
