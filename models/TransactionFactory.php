<?php

abstract class TransactionFactory extends ModelFactory {
    protected static $dbName = 'iugo';
    protected static $tableName = 'Transaction';

    public function __construct(){

    }
    
    /*
        Example: 
        $props = array('Username' => 'AAA');

    */
    public static function buildNewModel($props){
        return parent::buildNewModel($props);
    }
    
    /*
        Example: 
        $props = array(
            array('Username' => 'AAA'),
            array('Username' => 'BBB'),
            ...
        );

    */
    public static function buildNewModels($props){
        return parent::buildNewModels($props);
    }

    public static function deleteModel(Model $model = null){
        return parent::deleteModel($model);
    }

    public static function getAllModels(){
        $console = new MySQLConsole(static::$dbName);
        $results = $console->getAllRows(static::$tableName);

        $models = array();
        if(!empty($results)){
            foreach($results as $result){
                $models[] = new Transaction($result);
            }
        }
        return $models;
    }
    
    /*
        Example: 
        $props = array(
            array(
                'col' => 'Id',
                'comp' => '=',
                'val' => 1
            ),
            ...
        );

    */
    public static function getModelsByProperties($props){
        $console = new MySQLConsole(static::$dbName);
        $results = $console->select(static::$tableName, $props);

        $models = array();
        if(!empty($results)){
            foreach($results as $result){
                $models[] = new Transaction($result);
            }
        }
        return $models;
    }
    
    public static function updateModel(Model $model = null){
        if(!empty($model)){
            $console = new MySQLConsole(static::$dbName);
    
            $properties = $model->getProperties(SYSTEM_COLS);
            
            $properties['WHERE'] = array(
                array(
                    'col' => 'TransactionId',
                    'comp' => '=',
                    'val' => $model->getProperty('TransactionId')
                )
            );
    
            if($console->updateRow(static::$tableName, $properties)){
                return true;
            }
        }

        return false;
    }
}