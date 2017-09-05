<?php

class ScorePostAPI extends endPoint {

    protected $userId;
    protected $leaderboardId;
    protected $score;

    public function __construct($objArray){
        if(empty($objArray)){
            throw new Exception('Empty JSON post data');
        }
        else if(!(array_key_exists("UserId",$objArray) && array_key_exists("LeaderboardId",$objArray))){
            throw new Exception('Invalid JSON post data');
        }
        
        $this->userId = $objArray['UserId'];
        $this->leaderboardId = $objArray['LeaderboardId'];
        $this->score = $objArray['Score'];
    }
    
    public function execute(){
        $userId = $this->userId;
        $leaderboardId = $this->leaderboardId;
        $score = $this->score;

        // Check if Leaderboard exists
        $leaderboard = LeaderboardFactory::getModelById($leaderboardId);

        if(empty($leaderboard)){
            // Create new leaderboard
            $leaderboardProps = array(
                'Id' => $leaderboardId,
                'LeaderboardName' => $leaderboardId
            );

            if(!LeaderboardFactory::buildNewModel($leaderboardProps)){
                throw new Exception('Database Insert Error');
            }
            
            $leaderboard = LeaderboardFactory::getModelById($leaderboardId);
        }

        // Check if LeaderboardLinkToUser exists
        $user = UserFactory::getModelById($userId);
        $lltu = $leaderboard->getLinkToUser($user);

        if(empty($lltu)){
            // Create LeaderboardLinkToUser
            ModelFactory::setTableName('LeaderboardLinkToUser');
            $linkProps = array(
                'LeaderboardId' => $leaderboardId,
                'UserId' => $userId,
                'Score' => $score
            );
            if(!ModelFactory::buildNewModel($linkProps)){
                throw new Exception('Database Insert Error');
            }

            $lltu = $leaderboard->getLinkToUser($user);
        }

        $hiScore = $lltu->getProperty('Score');
        $rank = '';

        // Compare and update Score with post Score
        if($score > $hiScore){
            if(!$lltu->setProperties(array('Score' => $score))){
                throw new Exception('Update Error');
            }

            ModelFactory::setTableName('LeaderboardLinkToUser');
            if(!ModelFactory::updateModel($lltu)){
                throw new Exception('Update Error');
            }
            $hiScore = $lltu->getProperty('Score');
        }

        // Get the rank
        $rank = $leaderboard->getUserRank($user);

        $returnArray = array(
            "UserId" => $userId,
            "LeaderboardId" => $leaderboardId,
            "Score" => $hiScore,
            "Rank" => $rank
        );

        return $returnArray;
    }

}