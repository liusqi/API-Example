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
			$endPoint = new TimeStampAPI();
			return $endPoint->execute();
		}
		else{
			throw new Exception('GET requests only');
		}
	}
	
	protected function Transaction(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);

			$endPoint = new TransactionAPI($objArray);

			return $endPoint->execute();
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function TransactionStats(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);
			
			$endPoint = new TransactionStatsAPI($objArray);

			return $endPoint->execute();
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function ScorePost(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);
			
			$endPoint = new ScorePostAPI($objArray);

			return $endPoint->execute();
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function LeaderboardGet(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);
			
			$endPoint = new LeaderboardGetAPI($objArray);

			return $endPoint->execute();
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function UserSave(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);
			
			$endPoint = new UserSaveAPI($objArray);

			return $endPoint->execute();
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function UserLoad(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);
			
			$endPoint = new UserLoadAPI($objArray);

			return $endPoint->execute();
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	/*
	
		Reset Endpoint
	*/
	protected function ResetAll(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);
			
			$endPoint = new ResetAllAPI($objArray);

			return $endPoint->execute();
		}
		else{
			throw new Exception('POST requests only');
		}
	}

	protected function Reset(){
		if($this->method == 'POST'){
			$json = file_get_contents('php://input');
			$objArray = json_decode($json,true);
			
			$endPoint = new ResetAPI($objArray);

			return $endPoint->execute();
		}
		else{
			throw new Exception('POST requests only');
		}
	}
}