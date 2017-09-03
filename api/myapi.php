<?php
/**
*
* Version: 1.0
* Concrete api class
*/

require_once 'requirements.php';

class MyAPI extends API {

    public function __construct($request, $origin){
        parent::__construct($request);
	}
	
	/* Add endpoints here */ 
	protected function TimeStamp(){
		if($this->method == 'GET'){
			return array('Timestamp' => time ());
		}
		else{
			throw new Exception('GET requests only');
		}
	}
	
	protected function Transaction(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);

			if(empty($objArray)){
				throw new Exception('Empty JSON post data');
			}
			else if(!(array_key_exists("TransactionId",$objArray) && array_key_exists("UserId",$objArray) && array_key_exists("CurrencyAmount",$objArray) && array_key_exists("Verifier",$objArray))){
				throw new Exception('Invalid JSON post data');
			}
			
			$secretKey = 'NwvprhfBkGuPJnjJp77UPJWJUpgC7mLz';
			$transactionId = $objArray['TransactionId'];
			$userId = $objArray['UserId'];
			$currencyAmount = $objArray['CurrencyAmount'];
			$verifier = $objArray['Verifier'];
			
			// Check if Transaction exists
			$transactions = TransactionFactory::getAllModels();

			foreach($transactions as $transaction){
				if($transactionId == $transaction->getProperty('Id')){
					throw new Exception('Transaction exsits');
				}
			}

			$hash = sha1($secretKey . $transactionId . $userId . $currencyAmount);
			
			if($hash == $verifier){
				// Insert new Transaction
				$transactionProps = array(
					'TransactionId' => $transactionId,
					'UserId' => $userId,
					'CurrencyAmount' => $currencyAmount,
					'Verifier' => $verifier
				);

				if(!TransactionFactory::buildNewModel($transactionProps)){
					throw new Exception('Database Insert Error');
				}
				
				return array('Success' => true);
			}
			
			return array('Success' => false);
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function TransactionStats(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);

			if(empty($objArray)){
				throw new Exception('Empty JSON post data');
			}
			else if(!array_key_exists("UserId",$objArray)){
				throw new Exception('Invalid JSON post data');
			}
			
			$userId = $objArray['UserId'];

			// Select Transactions from database
			$selectProps = array(
				array(
					'col' => 'UserId',
					'comp' => '=',
					'val' => $userId
				)
			);
			
			$transactions = TransactionFactory::getModelsByProperties($selectProps);
			$transactionCount = count($transactions);
			$sum = 0;

			if(!empty($transactions)){
				foreach($transactions as $transaction){
					$sum += $transaction->getProperty('CurrencyAmount');
				}
			}

			$returnArray = array(
				'UserId' => $userId,
				'TransactionCount' => $transactionCount,
				'CurrencySum' => $sum
			);

			return $returnArray;
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function ScorePost(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);

			if(empty($objArray)){
				throw new Exception('Empty JSON post data');
			}
			else if(!(array_key_exists("UserId",$objArray) && array_key_exists("LeaderboardId",$objArray))){
				throw new Exception('Invalid JSON post data');
			}
			
			$userId = $objArray['UserId'];
			$leaderboardId = $objArray['LeaderboardId'];
			$score = $objArray['Score'];

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
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function LeaderboardGet(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);

			if(empty($objArray)){
				throw new Exception('Empty JSON post data');
			}
			else if(!(array_key_exists("UserId",$objArray) && array_key_exists("LeaderboardId",$objArray))){
				throw new Exception('Invalid JSON post data');
			}
			
			$userId = $objArray['UserId'];
			$leaderboardId = $objArray['LeaderboardId'];
			$offset = $objArray['Offset'];
			$limit = $objArray['Limit'];

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
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function UserSave(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);

			if(empty($objArray)){
				throw new Exception('Empty JSON post data');
			}
			else if(!array_key_exists("UserId",$objArray)){
				throw new Exception('Invalid JSON post data');
			}
			
			$userId = $objArray['UserId'];
			$data = $objArray['Data'];

			$result = DataFactory::buildDataModels($userId, $data, "DATA");

			$returnArray = array(
				"Success" => $result
			);

			return $returnArray;
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function UserLoad(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);

			if(empty($objArray)){
				throw new Exception('Empty JSON post data');
			}
			else if(!array_key_exists("UserId",$objArray)){
				throw new Exception('Invalid JSON post data');
			}
			
			$userId = $objArray['UserId'];

			$result = DataFactory::getDataArray($userId, "DATA");

			return $result;
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	/*
	
		Reset Endpoint
	*/
	protected function Reset(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);

			if(empty($objArray)){
				throw new Exception('Empty JSON post data');
			}
			else if(!(array_key_exists("Username",$objArray) && array_key_exists("Password",$objArray))){
				throw new Exception('Invalid JSON post data');
			}
			
			$username = $objArray['Username'];
			$password = sha1($objArray['Password']);

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
		else{
			throw new Exception('POST requests only');
		}
	}
}