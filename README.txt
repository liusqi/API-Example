************************************************************************************************************************

URL: www.sunshinebigboy.com

File Index:

	- api
		|
		| - api.php ------------------------ Abstract API class, listens to request and sends response
		| - endPoint.php ------------------- EndPoint interface class
		| - myapi.php ---------------------- Concrete API class, connects all the endpoints
		| - leaderboardGetAPI.php
		| - resetAllAPI.php
		| - resetAPI.php
		| - scorePostAPI.php
		| - timeStampAPI.php
		| - transactionAPI.php
		| - transactionStatsAPI.php
		| - userLoadAPI.php
		| - userSaveAPI.php

	- models ------------------------------- This folder has all the models that manipulates data
		| - Data.php 
		| - DataFactory.php
		| - Leaderboard.php
		| - LeaderboardFactory.php
		| - Model.php
		| - ModelFactory.php
		| - Transaction.php
		| - TransactionFactory.php
		| - User.php
		| - UserFactory.php

	.htaccess ------------------------------ htaccess file
	dbInitScript.php ----------------------- Database initialization script
	mydatabase.sql ------------------------- Database schema dump file
	mySQLConsole.php ----------------------- Class that deals with database queries
	requirements.php ----------------------- all the requirements/dependencies
	test.php ------------------------------- Main script
