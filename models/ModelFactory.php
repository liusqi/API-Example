<?php

class ModelFactory {
    protected static $dbName = 'iugo';
    protected static $tableName = '';

    public function __construct(){
        
    }

    public function setTableName($tableName){
        static::$tableName = $tableName;
        return true;
    }

    public function getTableName(){
        return static::$tableName;
    }
    
    /*
        Example: 
        $props = array('Username' => 'AAA');

    */
    public static function buildNewModel($props){
        $console = new MySQLConsole(static::$dbName);

        return static::buildNewModels(array($props));
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
        $console = new MySQLConsole(static::$dbName);

        $results = $console->insertRows(static::$tableName, $props);

        if($results){
            return true;
        }

        return false;
    }

    public static function deleteModel(Model $model = null){
        if(!empty($model)){
            $console = new MySQLConsole(static::$dbName);
            
            $id = $model->getProperty('Id');

            $deleteQuery = array(
                array(
                    'col' => 'Id',
                    'comp' => '=',
                    'val' => $id
                )
            );

            if($console->deleteRow(static::$tableName, $deleteQuery)){
                return true;
            }
        }
        
        return false;
    }

    public static function getAllModels(){
        $console = new MySQLConsole(static::$dbName);
        $results = $console->getAllRows(static::$tableName);

        $models = array();
        if(!empty($results)){
            foreach($results as $result){
                $models[] = new Model($result);
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
            array(
                'col' => 'Name',
                'comp' => '='
                'val' => 'ABC',
                'key' => 'AND'
            )
            ...
        );

    */
    public static function getModelsByProperties($props){
        $console = new MySQLConsole(static::$dbName);
        $results = $console->select(static::$tableName, $props);

        $models = array();
        if(!empty($results)){
            foreach($results as $result){
                $models[] = new Model($result);
            }
        }
        return $models;
    }
    
    public static function getModelById($Id){
        $props = array(
            array(
                'col' => 'Id',
                'comp' => '=',
                'val' => $Id
            )
        );
        $models = static::getModelsByProperties($props);

        $model = null;

        if(!empty($models)){
            $model = $models[0];
        }
        return $model;
    }

    public static function updateModel(Model $model = null, $selectArray = null){
        if(!empty($model)){
            $console = new MySQLConsole(static::$dbName);
    
            $properties = $model->getProperties(SYSTEM_COLS);
            
            if(empty($selectArray)){
                $properties['WHERE'] = array(
                    array(
                        'col' => 'Id',
                        'comp' => '=',
                        'val' => $model->getProperty('Id')
                    )
                );
            }
            else{
                $properties['WHERE'] = $selectArray;
            }

            if($console->updateRow(static::$tableName, $properties)){
                return true;
            }
        }

        return false;
    }
}