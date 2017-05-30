<?php

namespace Boost;

//unstable

use NetworkRelations;
use DataProcessor\DataProcessor;

class Boost {

    public function __construct() {
        ;
    }

    public static function dStump($sample)
    {
      //  $sat = array();
        $predictedLabels = array();
        

        /* $sample must be 3-d matrix or array
         * an array that holds other 2-d array(s)
         * 
         * TODO: validate matrix or array
         */
       
       
        list($avgSAT, $sat) = self::avgSAT($sample,true);
        for($run =0; $run<count($sample);$run++)
        {
           // array_push($sat,$val=DataProcessor::integralImage($sample[$run]));
          //  echo"!--------!";
           // var_dump( array_sum($sat[$run]));
           // echo"!--------!";
          
           
            if((self::Sum2d($sat[$run]) - $avgSAT) > ($avgSAT/4))
            {
            array_push($predictedLabels,0);
            continue;
            }
            
             array_push($predictedLabels,1);
        }
        
        return $predictedLabels;         
    }

    /* use float -> floatBoost
     * use ada -> adaBoost
     */

    public static function train(
    $trainer = 'ada', $weakL = 'dStump', $sample, $labels, $runs = 100) {
        $probArray = [];
        for ($i = 0; $i < $sampleSize; $i++) {
            array_push($probArray, (1 / $sampleSize));
        }

        self::adaBoost($runs, $weakL, $sample, $labels, $probArray);
    }

    private static function adaBoost(
    $runs, $weakL, $sample, $labels, $probArray) {

        $error = [];
        $weight = [];
        $tracker = 0;
        $probArrayM = $probArray;
        $sampleSize = count($sample);
        // $probTempArray = [$probArrayM];
        $hypothesis = [];

        $eTemp = [];

        for ($i = 0; $i < $runs; $i++) {

            //use weakL to check and call weak learners
            $wkL = self::$weakL($probArrayM, $sample);

            //run through the results of the decision stump 
            for ($trck = 0; $trck < $wkL; $trck) {
                //check for misclassification
                if ($wkL[$trck] != $labels[$trck]) {
                    //send in error values
                    array_push($eTemp, $probArrayM[$trck]);
                }
            }

            array_push($error, array_sum($eTemp));

            //calculate weight
           array_push( $weight, (1 / 2 * (log((1 / $error[$i]) - 1))));



            $wyhTop = exp(-($weight[$i] * $weakL[$tracker] * $sample[$tracker]));


            for ($j = 0; $j < $sampleSize; $j++) {
                $wyhBottom += ($probArrayM[$j]) * (exp(-($weight[$i] * $weakL[$j] * $sample[$j])));
            }

             //update probability
            $probArrayM[$i] = ($probArrayM[$tracker] * $wyhTop) / ($wyhBottom);

            
            if ($tracker == ($sampleSize - 1)) {
                $tracker = 0;
            }
            
        }

        //store hypothesis
            array_push($hypothesis, NetworkRelations\NetworkRelations::hardLimit(
                        //may not work
                            function ()
                            {
                                $val = 0;
                                for($m=0;$m<$runs;$m++){
                                   $val += ($weight[$m] * $wkL[$m]);
                                    }

                                return $val;
                            }
                           )
                        
                        );
            

        return $hypothesis;
    }
    
    /*
     * computes the average of Summed Area Table(SAT)
     */
    public static function avgSAT($sample,$bool = false)
    {
         $sat = array();
         $sum = 0;
        for($run =0; $run< $total=count($sample);$run++)
        {
            array_push($sat,$val=DataProcessor::integralImage($sample[$run]));
            $sum += self::Sum2d($val);
        }
     
       $avg=($sum/$total);
       if($bool)
       {
           return  [$avg,$sat] ;
       }
            return $avg;   
    }
    
    public static function Sum2d($array2d)
    {
        $val = 0;
     foreach($array2d as $ar)
     {
         $val += array_sum($ar);
     }
     return $val;
    }
    

    private function floatBoost() {
        
    }

}
