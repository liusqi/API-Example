<?php

class TransactionAPI extends endPoint {

    protected $secretKey = 'NwvprhfBkGuPJnjJp77UPJWJUpgC7mLz';
    protected $transactionId;
    protected $userId;
    protected $currencyAmount;
    protected $verifier;

    public function __construct($objArray){
        if(empty($objArray)){
            throw new Exception('Empty JSON post data');
        }
        else if(!(array_key_exists("TransactionId",$objArray) && array_key_exists("UserId",$objArray) && array_key_exists("CurrencyAmount",$objArray) && array_key_exists("Verifier",$objArray))){
            throw new Exception('Invalid JSON post data');
        }

        $this->transactionId = $objArray['TransactionId'];
        $this->userId = $objArray['UserId'];
        $this->currencyAmount = $objArray['CurrencyAmount'];
        $this->verifier = $objArray['Verifier'];
    }
    
    public function execute(){
        $secretKey = $this->secretKey;
        $transactionId = $this->transactionId;
        $userId = $this->userId;
        $currencyAmount = $this->currencyAmount;
        $verifier = $this->verifier;
        
        // Check if Transaction exists
        $transactions = TransactionFactory::getAllModels();

        foreach($transactions as $transaction){
            if($transactionId == $transaction->getProperty('Id')){
                throw new Exception('Transaction exsits');
            }
        }

        $hash = sha1($secretKey . $transactionId . $userId . $currencyAmount);
        
        if($hash == $verifier){
            // Insert new Transaction
            $transactionProps = array(
                'TransactionId' => $transactionId,
                'UserId' => $userId,
                'CurrencyAmount' => $currencyAmount,
                'Verifier' => $verifier
            );

            if(!TransactionFactory::buildNewModel($transactionProps)){
                throw new Exception('Database Insert Error');
            }
            
            return array('Success' => true);
        }
        
        return array('Success' => false);
    }

}