<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SingleLPerceptron;

include '../NetworkRelations/NetworkRelations.php';
include '../Matrix/Matrix.php';

use NetworkRelations\NetworkRelations;
use Matrix\Matrix;
use ErrorException;
class SingleLPerceptron {
    private $iterations = 0;
    private $input = array();
    private $weight = array();
    private $targetOutput = array();
    private $bias = null;
    private $mat = null;
    private $index=0; private $trained=0;
    private $trainingTracker=0;
    private $relation = [
        'symhardlimit' => "symHardLimit",
        'hardlimit' => "hardLimit",
        'linear' => "linear",
        'satlinear' => "satLinear",
        'symsatlinear' => "symSatLinear",
        'positivelinear' => "positiveLinear",
        'logsigmoid' => "logSigmoid",
        'tansigmoid' => "tanSigmoid"
    ];
   private $netRel = null;
    function __construct($input, $targetOutput) {

        $this->input = $input;
     //   $this->weight = $weight;
        $this->targetOutput = $targetOutput;
        $this->mat = new Matrix();
        $this->netRel = new NetworkRelations();
        
    }

     function neuron($weight, $bias=null, $input, string $activationFunc) {
        //the number of neurons to train depends on the number of rows in the weight matrix
        // $result = array();
        // $desiredOutput = 'somevalue';
      //  echo "<br/>Input => ";  print_r($input);
        $inputNew=[$input];
        $wP = $this->mat->multiply($weight, $inputNew);
       // var_dump($bias);
        if ($bias == null && !is_int($bias)) {
            $bias = (-1 * $wP[0]);
           
        }
        $this->bias=$bias;
        //
        $r = $wP[0] + $bias;
        echo " Bias:  ".$bias;
        $output = self::transferFunction($activationFunc, $r);
        // $e = $desiredOutput - $output;
        // array_push($this->bias, $bias);
        // array_push($result, $r);  
        echo " output: ".$output;
        return $output;
    }

     function train(Array $weight,String $activationFunc) {
       //  var_dump($this->bias);
                $output = self::neuron($weight, $this->bias, $this->input[$this->index], $activationFunc);
                
                // check if output is equal to desired output;
                //use current input to check the current output
                $e = $this->targetOutput[$this->index] - $output;
                echo "<br/>Error: ".$e;
                
                if ($e == 0) {
                     echo "<br/>Number of iterations so far => ".++$this->iterations;
                    ++$this->trained;
                   // continue;
                    echo "<br/>Got it right ". $this->trained." time(s)<br/> with weight "  ; //print_r($weight);echo "<br/>";
                    //if the network got the desired output for about ten times return weight 
                   
                    if($this->trained == 10)
                    {
                        // the new weigth comprises of the bias as the last element of the array
                     //  print_r($weight);
                      array_push($weight[0], $this->bias);  
                      return $weight;
                    }
                    
                    //get index for the next input
                   $this->index= self::nextInput($this->input, $this->index);
                  // echo " shout  ".$this->index;
                  $weight= self::train($weight,  $activationFunc);
                    
                } elseif ($e == 1) {
                     
                    if($this->trainingTracker > 100)
                    {
                        return false;
                    }
                    echo "<br/>Number of iterations so far => ".++$this->iterations;
                    $weight = self::updateWeight($weight, $this->input[$this->index], true);
                 
                    $bias = self::updateBias($this->bias, $e);
                    $this->bias=$bias;
                
                    echo "<br/><br/> **New Weight ";
                                      //  print_r($weight);
                                        echo "<br/>new bias ".$bias;
                    //get index for the next input
                    $this->index= self::nextInput($this->input, $this->index);
                    $this->trainingTracker++; 
                    echo "<br/>Training Tracker: ".$this->trainingTracker."<br/><br/>";
                    $weight =self::train($weight, $activationFunc);
                  
                    
                  
                    
                } elseif ($e == -1) {
                    if($this->trainingTracker > 100)
                    {
                        return false;
                    }
                   
                    echo "<br/>Number of iterations so far => ".++$this->iterations;
                    $weight = self::updateWeight($weight, $this->input[$this->index], false);
                  
                    $bias = self::updateBias($this->bias, $e);
                    echo "<br/><br/> **New Weight  ";
                                     //   print_r($weight);
                                        echo "<br/>new bias  ".$bias;
                    //get index for the next input
                     $this->index= self::nextInput($this->input, $this->index); $this->trainingTracker++;  
                     echo "<br/>Training Tracker: ".$this->trainingTracker."<br/><br/>";
                     $weight= self::train($weight,  $activationFunc);
                     
                   
                }
                
                return $weight;
          
    }

   protected static function nextInput($array,$currentIndex)
   {
     
       if($currentIndex >= (count($array)-1))
       {
           return 0;
       }elseif($currentIndex < count($array))
       {
          // echo
           $currentIndex++;
           return $currentIndex;
       }
   }
    
    
   protected static function updateWeight($weightArr, $inputArr, $bool) {
        //matrix addition 
       $input=$inputArr;
       $weight=$weightArr[0];
       $val=0;
        $newWeight = array();
        if ($bool) {
            for ($i = 0; $i < count($weight); $i++) {
                $val += $weight[$i] + $input[$i];
                array_push($newWeight, $val);
                $val=0;
            }
        } else { 
            for ($i = 0; $i < count($weight); $i++) {
               // echo "<br/> ".($weight[$i] - $input[$i])." <br/> ";
                $val += ($weight[$i] - $input[$i]);
                array_push($newWeight, $val);
                $val=0;
               
            } //echo "new weight"; ;print_r($newWeight);
        }
        return  array($newWeight);
    }

    
     function updateBias($bias, $error) {
     
        $newBias = $bias + $error;
       $this->bias=$newBias;
        return $newBias;
    }

    
    
     function transferFunction($activationFunction, $val) {

        if (!in_array(strtolower($activationFunction), $this->relation)) {

            new \ErrorException("Error: Parameter one of Function " . __METHOD__ . " is not a valid activation Function");
          // return false;
        }
        $func =$this->relation[strtolower($activationFunction)];
        $activationFunc =  $this->netRel->$func($val);
              
      //  echo $activationFunc."Here to walk";
        return $activationFunc;
    }

}
