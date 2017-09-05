<?php

class ResetAPI extends endPoint {

    protected $username;
    protected $password;
    protected $tableName;
    protected $properties;

    public function __construct($objArray){
        if(empty($objArray)){
            throw new Exception('Empty JSON post data');
        }
        else if(!(array_key_exists("Username",$objArray) && array_key_exists("Password",$objArray))){
            throw new Exception('Invalid JSON post data');
        }
        
        $this->username = $objArray['Username'];
        $this->password = sha1($objArray['Password']);
        $this->tableName = $objArray['TableName'];
        $this->properties = $objArray['Properties'];
    }
    
    public function execute(){
        
        $username = $this->username;
        $password = $this->password;
        $tableName = $this->tableName;
        $properties = $this->properties;

        if($username !== 'Admin'){
            throw new Exception('Invalid User');
        }

        $props = array(
            array(
                'col' => 'Username',
                'comp' => '=',
                'val' => $username
            ),
            array(
                'col' => 'Password',
                'comp' => '=',
                'val' => $password
            )
        );

        $users = UserFactory::getModelsByProperties($props);

        if(empty($users)){
            throw new Exception('Invalid User');
        }

        // Reset
        $selectArray = array();
        foreach($properties as $key => $val){
            $selectArray[] = array(
                'col' => $key,
                'comp' => '=',
                'val' => $val
            );
        }
        
        ModelFactory::setTableName($tableName);
        $models = ModelFactory::getModelsByProperties($selectArray);

        if(!empty($models)){
            foreach($models as $model){
                // Soft delete
                $model->setProperties(array('Status' => 0));

                if(!ModelFactory::updateModel($model, $selectArray)){
                    throw new Exception('Reset Fail!');
                }
            }
        }

        return array('Success' => true);
    }

}