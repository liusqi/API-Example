<?php

abstract class DataFactory extends ModelFactory {
    protected static $dbName = 'iugo';
    protected static $tableName = 'Data';

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
                $models[] = new Data($result);
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
                $models[] = new Data($result);
            }
        }
        return $models;
    }
    
    public static function getModelByDataKey($dataKey){
        $props = array(
            array(
                'col' => 'DataKey',
                'comp' => '=',
                'val' => $dataKey
            )
        );
        $models = static::getModelsByProperties($props);

        $model = null;

        if(!empty($models)){
            $model = $models[0];
        }
        return $model;
    }

    public static function updateModel(Model $model = null){
        if(!empty($model)){
            $console = new MySQLConsole(static::$dbName);
    
            $properties = $model->getProperties(2);
            
            $properties['WHERE'] = array(
                array(
                    'col' => 'DataKey',
                    'comp' => '=',
                    'val' => $model->getProperty('DataKey')
                )
            );
            
            if($console->updateRow(static::$tableName, $properties)){
                return true;
            }
        }

        return false;
    }

    public static function updateModelByProperties($props){
        $console = new MySQLConsole(static::$dbName);

        $properties = $props;
        
        $properties['WHERE'] = array(
            array(
                'col' => 'DataKey',
                'comp' => '=',
                'val' => $props['DataKey']
            )
        );

        $oldModel = static::getModelByDataKey($props['DataKey']);
        if(!empty($oldModel)){
            $result[] = $properties;
            if($oldModel->getProperty('ParentKey') !== $props['ParentKey']){
                return $result;
            }
        }

        $result = $console->updateRow(static::$tableName, $properties);

        return $result;
    }

    public static function buildDataModels($userId, $dataArray, $parentKey){

        if(!empty($dataArray)){
            foreach($dataArray as $key => $subDataArray){
                if($key == $subDataArray){
                    throw new Exception('Invalid DataKey');
                }
                $props = array(
                    'DataKey' => $key,
                    'ParentKey' => $parentKey,
                    'UserId' => $userId,
                    'Data' => $subDataArray,
                );

                //If not existed build New
                if(!static::buildNewModel($props)){
                    // $returnArray[] = static::updateModelByProperties($props);
                    if(!static::updateModelByProperties($props)){
                        return false;
                    }
                }
// $returnArray[] = array($subDataArray);
                static::buildDataModels($userId, $subDataArray, $key);
            }
        }

        // return $returnArray;
        return true;
    }

    public static function getDataArray($userId, $parentKey){
        $childDataArray = array();
        $props = array(
            array(
                'col' => 'UserId',
                'comp' => '=',
                'val' => $userId
            ),
            array(
                'col' => 'ParentKey',
                'comp' => '=',
                'val' => $parentKey,
                'key' => 'AND'
            )
        );
        $models = static::getModelsByProperties($props);

        if(!empty($models)){
            foreach($models as $model){
                $dataKey = $model->getProperty('DataKey');
                if($model->getProperty('Data') == null){
                    $childDataArray[$dataKey] = static::getDataArray($userId, $dataKey);
                }
                else{
                    $childDataArray[$dataKey] = $model->getProperty('Data');
                }
            }
        }

        return $childDataArray;
    }
}