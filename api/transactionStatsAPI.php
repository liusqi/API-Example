<?php

class TransactionStatsAPI extends endPoint {

    protected $userId;

    public function __construct($objArray){
        if(empty($objArray)){
            throw new Exception('Empty JSON post data');
        }
        else if(!array_key_exists("UserId",$objArray)){
            throw new Exception('Invalid JSON post data');
        }
        
        $this->userId = $objArray['UserId'];
    }
    
    public function execute(){
        $userId = $this->userId;

        // Select Transactions from database
        $selectProps = array(
            array(
                'col' => 'UserId',
                'comp' => '=',
                'val' => $userId
            )
        );
        
        $transactions = TransactionFactory::getModelsByProperties($selectProps);
        $transactionCount = count($transactions);
        $sum = 0;

        if(!empty($transactions)){
            foreach($transactions as $transaction){
                $sum += $transaction->getProperty('CurrencyAmount');
            }
        }

        $returnArray = array(
            'UserId' => $userId,
            'TransactionCount' => $transactionCount,
            'CurrencySum' => $sum
        );

        return $returnArray;
    }

}