<?php

class ResetAllAPI extends endPoint {

    protected $username;
    protected $password;

    public function __construct($objArray){
        if(empty($objArray)){
            throw new Exception('Empty JSON post data');
        }
        else if(!(array_key_exists("Username",$objArray) && array_key_exists("Password",$objArray))){
            throw new Exception('Invalid JSON post data');
        }
        
        $this->username = $objArray['Username'];
        $this->password = sha1($objArray['Password']);
    }
    
    public function execute(){
        
        $username = $this->username;
        $password = $this->password;

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
                'val' => $password,
                'key' => 'AND'
            )
        );

        $users = UserFactory::getModelsByProperties($props);

        if(empty($users)){
            throw new Exception('Invalid User');
        }

        // Reset DB
        $console = new MySQLConsole('iugo');
        $result = $console->resetAll();
        
        return array('Success' => $result);
    }

}