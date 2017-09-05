<?php
define('SYSTEM_COLS', 3);

require_once 'api/api.php';
require_once 'api/endPoint.php';
require_once 'api/timeStampAPI.php';
require_once 'api/transactionAPI.php';
require_once 'api/transactionStatsAPI.php';
require_once 'api/scorePostAPI.php';
require_once 'api/leaderboardGetAPI.php';
require_once 'api/userSaveAPI.php';
require_once 'api/userLoadAPI.php';
require_once 'api/resetAllAPI.php';
require_once 'api/resetAPI.php';

require_once 'mySQLConsole.php';
require_once 'models/ModelFactory.php';
require_once 'models/Model.php';

require_once 'models/TransactionFactory.php';
require_once 'models/Transaction.php';
require_once 'models/LeaderboardFactory.php';
require_once 'models/Leaderboard.php';
require_once 'models/UserFactory.php';
require_once 'models/User.php';
require_once 'models/DataFactory.php';
require_once 'models/Data.php';