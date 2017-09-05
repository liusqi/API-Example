<?php

class UserSaveAPI extends endPoint {

    protected $userId;
    protected $data;
    protected $root = 'DATA';

    public function __construct($objArray){
        if(empty($objArray)){
            throw new Exception('Empty JSON post data');
        }
        else if(!array_key_exists("UserId",$objArray)){
            throw new Exception('Invalid JSON post data');
        }
        
        $this->userId = $objArray['UserId'];
        $this->data = $objArray['Data'];

    }
    
    public function execute(){
        
        $userId = $this->userId;
        $data = $this->data;

        $result = DataFactory::buildDataModels($userId, $data, $this->root);

        $returnArray = array(
            "Success" => $result
        );

        return $returnArray;
    }

}