<?php

class Transaction extends Model{
    public $properties = array();

    public function __construct($properties){
        $this->properties = $properties;
    }

    public function getProperty($colName){
        return parent::getProperty($colName);
    }
    
    public function getProperties($offset = 0){
        return parent::getProperties($offset);
    }
    
    /*
        Example: 
        $props = array('Username' => 'AAA')

    */
    public function setProperties($props){
        return parent::setProperties($props);
    }

    public function delete(){
        if(TransactionFactory::deleteModel($this)){
            return true;
        }

        return false;
    }

    private function save(){
        if(TransactionFactory::updateModel($this)){
            return true;
        }

        return false;
    }
}