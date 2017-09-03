<?php
/**
*
* Version: 1.0
* Mysql console class
*
*/
class MySQLConsole{
    private $servername = "localhost:3306";
    private $username = "root";
    private $password = "root";
    private $dbName = '';
    private $query;
    private $errorMessage;
    public $connection;
    
    public function __construct($dbName = null){
        $this->setDB($dbName);
    }

    public function createDB($dbName){
        $connect = $this->getConnection();
        $query = 'CREATE DATABASE IF NOT EXISTS ' . $dbName . ';';

        if ($connect->query($query) === TRUE) {
            $this->close();
            return true;
        }

        $this->errorMessage = $connect->connect_error;
        $this->close();
        return false;
    }

    public function createTable($tableName, $props){
        $connect = $this->getConnection();

        $query = 'DROP TABLE IF EXISTS ' . $tableName . ';';

        $connect->query($query);
        $primaryKey = '';

        $query = 'CREATE TABLE IF NOT EXISTS ' . $tableName . '(';
        
        foreach($props as $key => $val){
            if($key == 'FOREIGN KEY'){
                foreach($val as $fkCol => $fkRef ){
                    $query .= 'FOREIGN KEY (' . $fkCol . ') REFERENCES ' . $fkRef . ',';
                }
            }
            else if($key == 'PRIMARY KEY'){
                $primaryKey = $val;
            }
            else{
                $query .=  $key . ' ' . $val . ',';
            }
        }

        if(empty($primaryKey)){
            $query .= 'PRIMARY KEY (Id)';
        }
        else{
            $query .= 'PRIMARY KEY (' . $primaryKey . ')';
        }
        
        $query .= ');';

        if ($connect->query($query) === TRUE) {
            $this->close();
            return true;
        }

        $this->errorMessage = $connect->error;
        $this->close();
        return false;
    }

    public function insertRows($tableName, $props){
        $connect = $this->getConnection();

        $query = 'INSERT INTO ' . $tableName;
        $columns = '';
        $values = '';
        $i = 0;

        foreach($props as $prop){
            $values .= '(';

            foreach($prop as $key => $val){
                if($i == 0){
                    $columns .=  $key . ',';
                }
                
                if(is_null($val) || is_array($val)){
                    $values .= 'NULL,';
                }
                else if(is_string($val)){
                    $values .= '"' . $val . '",';
                }
                else{
                    $values .= $val . ',';
                }
                
            }

            $i = 1;
            $values = $this->cleanQuery($values);
            $values .= '),';
        }

        $columns = $this->cleanQuery($columns);
        $values = $this->cleanQuery($values);

        $query = $query . ' (' . $columns . ') VALUES ' . $values . ';';

        if ($connect->query($query) === TRUE) {
            $this->close();
            return true;
        }

        $this->errorMessage = $connect->error;
        $this->close();
        return false;
    }

    public function deleteRow($tableName, $props){
        $connect = $this->getConnection();

        $query = 'DELETE FROM ' . $tableName . ' WHERE ';

        foreach($props as $prop){
            $query .= $prop['key'] . ' ' . $prop['col'] . $prop['comp'];

            if(is_string($prop['val'])){
                $query .= '"' . $prop['val'] . '" ';
            }
            else{
                $query .= $prop['val'] . ' ';
            }
        }

        $query = $this->cleanQuery($query);

        $query .= ';';

        if ($connect->query($query) === TRUE) {
            $this->close();
            return true;
        }

        $this->errorMessage = $connect->error;
        $this->close();
        return false;
    }

    public function updateRow($tableName, $props){
        $connect = $this->getConnection();

        $query = 'UPDATE ' . $tableName . ' SET ';

        foreach($props as $key => $val){
            if($key !== 'WHERE'){
                $query .= $key . '=';
                
                if(is_null($val) || is_array($val)){
                    $query .= 'NULL,';
                }
                else if(is_string($val)){
                    $query .= '"' . $val . '",';
                }
                else{
                    $query .= $val . ',';
                }
            }
        }

        $query = $this->cleanQuery($query);

        $query .= ' WHERE ';

        foreach($props['WHERE'] as $key => $prop){
            if(!empty($prop)){
                $query .= $prop['key'] . ' ' . $prop['col'] . $prop['comp'];
                
                if(is_string($prop['val'])){
                    $query .= '"' . $prop['val'] . '" ';
                }
                else{
                    $query .= $prop['val'] . ' ';
                }
            }
        }

        $query .= ';';

        if ($connect->query($query) === TRUE) {
            $this->close();
            return true;
        }

        $this->errorMessage = $connect->error;
        $this->close();
        return false;
    }
    
    public function getAllRows($tableName){
        $connect = $this->getConnection();

        $query = 'SELECT * FROM ' . $tableName . ';';

        $result = $connect->query($query);
        $returnArray = array();
        
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                foreach ($row as &$val) {
                    if (is_numeric($val))
                        $val = (int)$val;
                }
                $returnArray[] = $row;
            }
        }

        $this->errorMessage = $connect->error;
        $this->close();
        return $returnArray;
    }

    public function select($tableName, $props, $column = '*'){
        $connect = $this->getConnection();

        $query = 'SELECT ' . $column . ' FROM ' . $tableName . ' WHERE ';

        foreach($props as $prop){
            if($prop['key'] == 'ORDER BY'){
                $query .= $prop['key'] . ' ' . $prop['col'] . ' ';
            }
            else if(!empty($prop)){
                $query .= $prop['key'] . ' ' . $prop['col'] . $prop['comp'];
                
                if(is_string($prop['val'])){
                    $query .= '"' . $prop['val'] . '" ';
                }
                else{
                    $query .= $prop['val'] . ' ';
                }
            }
        }

        $query .= ';';

        $result = $connect->query($query);
        $returnArray = array();
        
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                foreach ($row as &$val) {
                    if (is_numeric($val))
                        $val = (int)$val;
                }
                $returnArray[] = $row;
            }
        }

        $this->errorMessage = $connect->error;
        $this->close();
        return $returnArray;
    }
    
    public function setDB($dbName){
        $this->dbName = $dbName;
        return true;
    }

    public function getError(){
        return $this->errorMessage;
    }

    private function cleanQuery($query){
        return substr($query, 0, -1);
    }

    public function runQueries($queryArray){
        $connect = $this->getConnection();
        $resultArray = array();
        foreach($queryArray as $query){
            $resultArray[] = $connect->query($query);
        }
        $this->close();
        return $resultArray;
    }

    public function getConnection(){
        if(empty($this->connection)){
            $this->connection = new mysqli($this->servername, $this->username, $this->password, $this->dbName);
        }

        return $this->connection;
    }

    public function close(){
        $connect = $this->getConnection();
        $connect->close();
        unset($this->connection);

        return true;
    }

    public function resetAll(){
        $console = new MySQLConsole();

        $queryArray = array(
            'DROP DATABASE iugo;',
            'CREATE DATABASE iugo;'
        );
        $console->runQueries($queryArray);

        if(!$console->createDB('iugo')){
            throw new Exception('DB Can\'t be created! Error: ' . $console->getError());
        }
        
        if(!$console->setDB('iugo')){
            throw new Exception('DB Select Fail');
        }
        
        //Create Tables
        // User Table
        $userColumns = array(
            'Id' => 'INT NOT NULL AUTO_INCREMENT',
            'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'Username' => 'VARCHAR(255) UNIQUE',
            'Password' => 'VARCHAR(255)'
        );
        
        if(!$console->createTable('User', $userColumns)){
            throw new Exception('Table Can\'t be created! Error: User table ' . $console->getError());
        }
        
        // Transaction Table
        $transactionColumns = array(
            'Id' => 'INT NOT NULL AUTO_INCREMENT',
            'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'TransactionId' => 'INT',
            'UserId' => 'INT',
            'CurrencyAmount' => 'INT',
            'Verifier' => 'VARCHAR(255)',
            'FOREIGN KEY' => array(
                'UserId' => 'User(Id)'
            )
        );
        
        if(!$console->createTable('Transaction', $transactionColumns)){
            throw new Exception('Table Can\'t be created! Error: Transaction table ' . $console->getError());
        }
        
        // Leaderboard Table
        $leaderboardColumns = array(
            'Id' => 'INT NOT NULL AUTO_INCREMENT',
            'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'LeaderboardName' => 'VARCHAR(255)'
        );
        
        if(!$console->createTable('Leaderboard', $leaderboardColumns)){
            throw new Exception('Table Can\'t be created! Error: Leaderboard table ' . $console->getError());
        }
        
        // Leaderboard Link To User Table
        $leaderboardLinkToUserColumns = array(
            'Id' => 'INT NOT NULL AUTO_INCREMENT',
            'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'UserId' => 'INT',
            'LeaderboardId' => 'INT',
            'Score' => 'INT',
            'FOREIGN KEY' => array(
                'UserId' => 'User(Id)',
                'LeaderboardId' => 'Leaderboard(Id)'
            )
        );
        
        if(!$console->createTable('LeaderboardLinkToUser', $leaderboardLinkToUserColumns)){
            throw new Exception('Table Can\'t be created! Error: LeaderboardLinkToUser table ' . $console->getError());
        }
        
        $dataColumns = array(
            'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
            'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'DataKey' => 'VARCHAR(255) NOT NULL',
            'ParentKey' => 'VARCHAR(255)',
            'UserId' => 'INT',
            'Data' => 'VARCHAR(255)',
            'PRIMARY KEY' => 'DataKey, ParentKey, UserId',
            'FOREIGN KEY' => array(
                'UserId' => 'User(Id)'
            )
        );
        
        if(!$console->createTable('Data', $dataColumns)){
            throw new Exception('Table Can\'t be created! Error: Data table ' . $console->getError());
        }
        
        //-------------------------------------------------------------------------------------------------------------------------------------
        // test
        //Insert Users
        $userProps = array(
            array(
                'Username' => 'Admin',
                'Password' => '2cd85edd1a61fb1f60d51be16b90175d81f4e585'
            ),
            array(
                'Username' => 'John',
                'Password' => ''
            ),
            array(
                'Username' => 'Jack',
                'Password' => ''
            ),
            array(
                'Username' => 'Jim',
                'Password' => ''
            ),
            array(
                'Username' => 'Joker',
                'Password' => ''
            )
        );
        
        if(!$console->insertRows('User', $userProps)){
            throw new Exception('Error: ' . $console->getError());
        }
        
        $transactionProps = array(
            array(
                'TransactionId' => 1,
                'UserId' => 2,
                'CurrencyAmount' => 3,
                'Verifier' => 'fd6b91387c2853ac8467bb4d90eac30897777fc6'
            ),
            array(
                'TransactionId' => 2,
                'UserId' => 2,
                'CurrencyAmount' => 3,
                'Verifier' => 'be0ff1ef1f092f0f04c25b96555ae9097781f801'
            ),
            array(
                'TransactionId' => 3,
                'UserId' => 2,
                'CurrencyAmount' => -1,
                'Verifier' => '260fa52dc7030d405bbf40612409703f1edf06f2'
            ),
            array(
                'TransactionId' => 4,
                'UserId' => 1,
                'CurrencyAmount' => 3,
                'Verifier' => 'b2a8b5417223c58905c9f30d03edb3ce0dacd0be'
            )
        );
        
        if(!$console->insertRows('Transaction', $transactionProps)){
            throw new Exception('Error: ' . $console->getError());
        }

        return true;
    }
}