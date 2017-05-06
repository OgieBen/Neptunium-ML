<?php

namespace HebbSupervised;

include '../Matrix/Matrix.php';

class HebbSupervised {

    private $inputData = array();
    private $desiredOutputs = array();
    private $matrix;

    public function __construct(Array $desiredOutputs,Array $inputData) {
        $this->inputData = $inputData;
        $this->desiredOutputs = $desiredOutputs;
        $this->matrix = new \Matrix\Matrix();
    }

    // function belongs to Matrix call but it uses this function
    private static function psuedoInverse() {
        
    }

    //returns weight of the network
    private static function getInitialWeight( ) {
        //do not forget that $inputs must be the transpose of your input data matrix
       
        $mtTrans = $this->matrix->transformToColumn( $this->inputData );
        $initialWeight = $this->matrix->multiply($this->desiredOutputs, $mtTrans);
        $weight = $this->matrix->transformToRow($initialWeight, $this->matrix->getDimCol());
        return $weight;
    }

    //return new weight: calculated from the old one
    //may not be necessary for supervised
    private static function getNewWeight() {
        
    }

    //this function uses the (simple) linear associator as its network ach
    //other arch can be design in tis manner also
    static function linearAssioAch($weight, $newInput) {

        $transformNewMatrix = $this->matrix->transformToColumn( $newInput );
        $output = $this->matrix->multiply($weight, $transformNewMatrix);
       $result = $this->matrix->transformToRow($output, $this->matrix->getDimCol());
    
        return $result;
    }
    
    
    private static function orthogonalityChecker()
    {
      //  $this->;
        
    }

}
