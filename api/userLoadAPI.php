<?php

class UserLoadAPI extends endPoint {

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

        $result = DataFactory::getDataArray($userId, "DATA");

        return $result;
    }

}