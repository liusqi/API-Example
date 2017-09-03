<?php

class User extends Model {
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
        if(UserFactory::deleteModel($this)){
            return true;
        }

        return false;
    }

    private function save(){
        if(UserFactory::updateModel($this)){
            return true;
        }

        return false;
    }

    // public function getRankInLeaderboard(Leaderboard $leaderboard){
    //     $rank = 0;
    //     $link = $leaderboard->getLinkToUser($this);

    //     if(!empty($link)){
    //         $selectProps = array(
    //             array(
    //                 'col' => 'Score',
    //                 'comp' => '>=',
    //                 'val' => $link->getProperty('Score')
    //             ),
    //             array(
    //                 'col' => 'LeaderboardId',
    //                 'comp' => '=',
    //                 'val' => $leaderboard->getProperty('Id'),
    //                 'key' => 'AND'
    //             ),
    //             array(
    //                 'col' => 'Score DESC',
    //                 'key' => 'ORDER BY'
    //             )
    //         );

    //         // $console = new MySQLConsole('iugo');
    //         // $results = $console->select('LeaderboardLinkToUser', $selectProps, '*');

    //         ModelFactory::setTableName('LeaderboardLinkToUser');
    //         $results = ModelFactory::getModelsByProperties($selectProps);
    //         $rank = count($results);
    //     }
    //     return $rank;
    // }
}