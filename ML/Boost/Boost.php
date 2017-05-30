<?php

namespace Boost;

use NetworkRelations;
use DataProcessor\DataProcessor;

class Boost {

    public function __construct() {
        ;
    }

    public static function dStump($sample) {
        //  $sat = array();
        $predictedLabels = array();


        /* $sample must be 3-d matrix or array
         * an array that holds other 2-d array(s)
         * 
         * TODO: validate matrix or array
         */


        list($avgSAT, $sat) = self::avgSAT($sample, true);
        for ($run = 0; $run < count($sample); $run++) {
            // array_push($sat,$val=DataProcessor::integralImage($sample[$run]));
            //  echo"!--------!";
            // var_dump( array_sum($sat[$run]));
            // echo"!--------!";


            if ((self::Sum2d($sat[$run]) - $avgSAT) > ($avgSAT / 4)) {
                array_push($predictedLabels, 0);
                continue;
            }

            array_push($predictedLabels, 1);
        }


        return $predictedLabels;
    }

    /* use float -> floatBoost
     * use ada -> adaBoost
     */

    public static function train(
    $trainer = 'ada', $weakL = ['dStump'], $sample, $labels, $runs = 100) {
        $probArray = [];
        $sampleSize = count($sample);
        for ($i = 0; $i < $sampleSize; $i++) {
            array_push($probArray, (1 / $sampleSize));
        }

        $boost = strtolower($trainer) . 'Boost';
        return self::$boost($weakL, $sample, $labels, $probArray);
    }

    private static function adaBoost($weakL, $sample, $labels, $probArray) {

        $error = [];
        $weight = [];
        $tracker = 0;
        $temp = array();
        $probArrayM = $probArray;
        $sampleSize = count($sample);
        // $probTempArray = [$probArrayM];
        $hypothesis = [];
        $wyhTop = 0;
        $wyhBottom = 0;
        $eTemp = [];

        for ($i = 0; $i < count($weakL); $i++) {

            //use weakL to check and call weak learners

            $learner = $weakL[$i];
            $wkL = self::$learner($sample);
            array_push($hypothesis, $wkL);

            //run through the results of the decision stump 
            for ($trck = 0; $trck < count($wkL); $trck++) {
                //check for misclassification
                $temp[$trck] = ($wkL[$trck] != $labels[$trck] );
                if ($temp[$trck]) {
                    // var_dump($wkL[$trck]);
                    //send in weight values of the misclassified samples
                    $eTemp[$trck] = $probArrayM[$trck];
                }
            }

            array_push($error, $e = array_sum($eTemp));

            //calculate weight
            array_push($weight, $w = (0.5 * (log((1 / $e) - 1))));

            for ($j = 0; $j < $sampleSize; $j++) {

                $wyhBottom += ($probArrayM[$j]) * (exp(-($w * $wkL[$j] * $labels[$j])));
                var_dump($wyhBottom);
            }

            $f = 0;
            foreach ($temp as $predLabel) {
                if ($predLabel) {
                    $wyhTop = $probArrayM[$f] * exp(-($w * $wkL[$f] * $labels[$f]));
                    $probArrayM[$f] = ($wyhTop / $wyhBottom);
                }
                $f++;
            }



            $eTemp = array();
        }

        return [$weight, $hypothesis];
    }

    public static function evaluateStrgHyp($weight, $hypothesis, $sampleId) {
        $pred = 0;

        for ($i = 0; $i < count($weight); $i++) {

            $pred += $weight[$i] * $hypothesis[$i][$sampleId];
        }
        return $pred;
    }

    /*
     * computes the average of Summed Area Table(SAT)
     */

    public static function avgSAT($sample, $bool = false) {
        $sat = array();
        $sum = 0;
        for ($run = 0; $run < $total = count($sample); $run++) {
            array_push($sat, $val = DataProcessor::integralImage($sample[$run]));
            $sum += self::Sum2d($val);
        }

        $avg = ($sum / $total);
        if ($bool) {
            return [$avg, $sat];
        }
        return $avg;
    }

    public static function Sum2d($array2d) {
        $val = 0;
        foreach ($array2d as $ar) {
            $val += array_sum($ar);
        }
        return $val;
    }

    private function floatBoost() {
        
    }

}
