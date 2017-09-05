<?php

class LeaderboardGetAPI extends endPoint {

    protected $userId;
    protected $leaderboardId;
    protected $offset;
    protected $limit;

    public function __construct($objArray){
        if(empty($objArray)){
            throw new Exception('Empty JSON post data');
        }
        else if(!(array_key_exists("UserId",$objArray) && array_key_exists("LeaderboardId",$objArray))){
            throw new Exception('Invalid JSON post data');
        }
        
        $this->userId = $objArray['UserId'];
        $this->leaderboardId = $objArray['LeaderboardId'];
        $this->offset = $objArray['Offset'];
        $this->limit = $objArray['Limit'];
    }
    
    public function execute(){
        $userId = $this->userId;
        $leaderboardId = $this->leaderboardId;
        $offset = $this->offset;
        $limit = $this->limit;

        $leaderboard = LeaderboardFactory::getModelById($leaderboardId);
        if(empty($leaderboard)){
            throw new Exception('Leaderboard does not exist');
        }

        $user = UserFactory::getModelById($userId);
        if(empty($user)){
            throw new Exception('User does not exist');
        }

        $rank = $leaderboard->getUserRank($user);
        $score = $leaderboard->getUserScore($user);

        //GetEntriesArray
        $entriesArray = $leaderboard->getRankArray($offset, $limit);

        $returnArray = array(
            "UserId" => $userId,
            "LeaderboardId" => $leaderboardId,
            "Score" => $score,
            "Rank" => $rank,
            "Entries" => $entriesArray
        );

        return $returnArray;
    }

}