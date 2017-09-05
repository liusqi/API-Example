<?php
/**
*
* This is a database initialization script.
*
*/
require_once 'requirements.php';
echo 'You don\t have access to this file!';
die();

//-------------------------------------------------------------------Init Start-----------------------------------------------------------------------
$console = new MySQLConsole();

$queryArray = array(
    'DROP DATABASE iugo;',
    'CREATE DATABASE iugo;'
);
$console->runQueries($queryArray);

if(!$console->createDB('iugo')){
    echo 'DB Can\'t be created<br>';
    echo 'error: ' . $console->getError();
    return false;
}

echo 'DB Created<br>';

if(!$console->setDB('iugo')){
    echo 'DB Select Fail<br>';
    return false;
}

echo 'DB Selected<br>';

//Create Tables

// User Table
$userColumns = array(
    'Id' => 'INT NOT NULL AUTO_INCREMENT',
    'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
    'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'Status' => 'BOOL NOT NULL DEFAULT 1',
    'Username' => 'VARCHAR(255) UNIQUE',
    'Password' => 'VARCHAR(255)'
);

if(!$console->createTable('User', $userColumns)){
    echo 'Table Can\'t be created<br>';
    echo 'error: Transaction table ' . $console->getError();
    return false;
}

// Transaction Table
$transactionColumns = array(
    'TransactionId' => 'INT NOT NULL AUTO_INCREMENT',
    'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
    'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'Status' => 'BOOL NOT NULL DEFAULT 1',
    'UserId' => 'INT',
    'CurrencyAmount' => 'INT',
    'Verifier' => 'VARCHAR(255)',
    'PRIMARY KEY' => 'TransactionId',
    'FOREIGN KEY' => array(
        'UserId' => 'User(Id)'
    )
);

if(!$console->createTable('Transaction', $transactionColumns)){
    echo 'Table Can\'t be created<br>';
    echo 'error: Transaction table ' . $console->getError();
    return false;
}

// Leaderboard Table
$leaderboardColumns = array(
    'Id' => 'INT NOT NULL AUTO_INCREMENT',
    'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
    'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'Status' => 'BOOL NOT NULL DEFAULT 1',
    'LeaderboardName' => 'VARCHAR(255)'
);

if(!$console->createTable('Leaderboard', $leaderboardColumns)){
    echo 'Table Can\'t be created<br>';
    echo 'error: Transaction table ' . $console->getError();
    return false;
}

// Leaderboard Link To User Table
$leaderboardLinkToUserColumns = array(
    'Id' => 'INT NOT NULL AUTO_INCREMENT',
    'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
    'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'Status' => 'BOOL NOT NULL DEFAULT 1',
    'UserId' => 'INT',
    'LeaderboardId' => 'INT',
    'Score' => 'INT',
    'FOREIGN KEY' => array(
        'UserId' => 'User(Id)',
        'LeaderboardId' => 'Leaderboard(Id)'
    )
);

if(!$console->createTable('LeaderboardLinkToUser', $leaderboardLinkToUserColumns)){
    echo 'Table Can\'t be created<br>';
    echo 'error: Transaction table ' . $console->getError();
    return false;
}

$dataColumns = array(
    'Inception' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
    'UpdateDatetime' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'Status' => 'BOOL NOT NULL DEFAULT 1',
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
    echo 'Table Can\'t be created<br>';
    echo 'error: Data table ' . $console->getError();
    return false;
}

echo 'Tables Created<br>';

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

if($console->insertRows('User', $userProps)){
    echo 'SUCCEED!';
}
else{
    echo 'error: ' . $console->getError();
    return false;
}

$result = $console->getAllRows('User');

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

if($console->insertRows('Transaction', $transactionProps)){
    echo 'SUCCEED!';
}
else{
    echo 'error: ' . $console->getError();
    return false;
}

// $deleteProps = array(
//     'TransactionId' => 1
// );

// $console->deleteRow('Transaction', $deleteProps);

// $result = $console->getAllRows('Transaction');
// var_dump($result); echo '<br>';

// $selectProps = array(
//     '' => array(
//         'col' => 'UserId',
//         'comp' => '=',
//         'val' => '2'
//     ),
//     'and' => array()
// );

// $result = $console->select('Transaction', $selectProps);
// var_dump($result); echo '<br>';


// $console = new MySQLConsole('iugo');

// $transaction = new Transaction();
// $transactions = $transaction->getAllModels();
// var_dump($transactions);

// $users = UserFactory::getAllModels();
// $id = $users[0]->getProperty('Username');
// $user = $users[0];


// $props = array(
//         "TransactionId" => 1,
//         "UserId" => 2,
//         "CurrencyAmount" => 3,
//         "Verifier" => 'aaa'
//     );

// TransactionFactory::buildNewModel($props);

// $transactions = TransactionFactory::getAllModels();
// echo '<pre>'; var_dump(); die();
// $props = array(
//     array(
//         'col' => 'Id',
//         'comp' => '=',
//         'val' => 4
//     )
// );

// $users = UserFactory::getModelsByProperties($props);

// $user = $users[0];
// UserFactory::deleteModel($user);

// echo '<pre>'; var_dump($users); die();

// $users = UserFactory::getAllModels();
// $user = $users[3];

// $result = $user->getProperties();
// echo '<pre>'; var_dump($result); die();

// $console = new MySQLConsole('iugo');

// $queryArray = array(
//     'SET @rank:=0;',
//     'SELECT (@rank:=@rank + 1) AS Rank, UserId, LeaderboardId, Score FROM LeaderboardLinkToUser WHERE LeaderboardId=1 ORDER BY Score DESC, UpdateDatetime DESC;'
// );

// $resultArray = $console->runQueries($queryArray);
// $returnArray = array();

// if ($resultArray[1]->num_rows > 0) {
//     // output data of each row
//     while($row = $resultArray[1]->fetch_assoc()) {
//         foreach ($row as &$val) {
//             if (is_numeric($val))
//                 $val = (int)$val;
//         }
//         $returnArray[] = $row;
//     }
// }

// echo '<pre>'; var_dump($returnArray);
// die();