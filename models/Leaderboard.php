<?php

class Leaderboard extends Model {
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
        if(LeaderboardFactory::deleteModel($this)){
            return true;
        }

        return false;
    }

    private function save(){
        if(LeaderboardFactory::updateModel($this)){
            return true;
        }

        return false;
    }
    
    /*
        return Model
    */
    public function getLinkToUser(User $user){
        $userId = $user->getProperty('Id');
        $Id = $this->getProperty('Id');

        $selectProps = array(
            array(
                'col' => 'UserId',
                'comp' => '=',
                'val' => $userId
            ),
            array(
                'col' => 'LeaderboardId',
                'comp' => '=',
                'val' => $Id,
                'key' => 'AND'
            )
        );

        ModelFactory::setTableName('LeaderboardLinkToUser');
        $results = ModelFactory::getModelsByProperties($selectProps);
        if(!empty($results)){
            return $results[0];
        }

        return $results;
    }

    /*
        return rank array
    */
    public function getRankArray($offset = null, $limit = null){
        $console = new MySQLConsole('iugo');
        
        $queryArray = array(
            'SET @rank:=0;',
            'SELECT UserId, Score, (@rank:=@rank + 1) AS Rank FROM LeaderboardLinkToUser WHERE LeaderboardId=' . $this->getProperty('Id') . ' ORDER BY Score DESC, UpdateDatetime DESC;'
        );

        $resultArray = $console->runQueries($queryArray);
        $returnArray = array();
        
        if ($resultArray[1]->num_rows > 0) {
            // output data of each row
            while($row = $resultArray[1]->fetch_assoc()) {
                foreach ($row as &$val) {
                    if (is_numeric($val))
                        $val = (int)$val;
                }
                $returnArray[] = $row;
            }
        }

        if(!is_null($offset)){
            if(!is_null($limit)){
                $returnArray = array_slice($returnArray, $offset, $limit);
            }
            else{
                $returnArray = array_slice($returnArray, $offset);
            }
        }

        return $returnArray;
    }

    public function getUserRank(User $user){
        $rankArray = $this->getRankArray();
        $userId = $user->getProperty('Id');
        $rank = 0;

        if(!empty($rankArray)){
            foreach($rankArray as $rankRecord){
                if($rankRecord['UserId'] == $userId){
                    $rank = $rankRecord['Rank'];
                }
            }
        }

        return $rank;
    }

    public function getUserScore(User $user){
        $rankArray = $this->getRankArray();
        $userId = $user->getProperty('Id');
        $score = 0;
        
        if(!empty($rankArray)){
            foreach($rankArray as $rankRecord){
                if($rankRecord['UserId'] == $userId){
                    $score = $rankRecord['Score'];
                }
            }
        }

        return $score;
    }
}