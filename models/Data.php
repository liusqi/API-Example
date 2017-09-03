<?php

class Data extends Model {
    public $properties = array();

    public function __construct($properties){
        $this->properties = $properties;
    }

    public function getProperty($colName){
        if(array_key_exists($colName, $this->properties)){
            return $this->properties[$colName];
        }
        return null;
    }
    
    public function getProperties($offset = 0){
        return array_slice($this->properties,$offset,count($this->properties) - $offset);
    }

    /*
        Example: 
        $props = array('Username' => 'AAA')

    */
    public function setProperties($props){
        foreach($props as $key => $val){
            if(array_key_exists($key, $this->properties)){
                $this->properties[$key] = $val;
            }
        }

        if($this->save()){
            return true;
        }

        return false;
    }

    public function delete(){
        if(DataFactory::deleteModel($this)){
            return true;
        }

        return false;
    }

    private function save(){
        if(DataFactory::updateModel($this)){
            return true;
        }

        return false;
    }
}