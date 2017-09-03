************************************************************************************************************************

URL: www.sunshinebigboy.com

File Index:

	- api
		|
		| - api.php ------------------------ Abstract API class, listens to request and sends response
		| - myapi.php ---------------------- Concrete API class, has all the endpoints

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
	dbInitScript.php ----------------------- Database initialization script, has all database schemas involved
	dumpfile.sql --------------------------- Database dump file
	hashGenerator.php
	mySQLConsole.php ----------------------- Class that deals with database queries
	requirements.php ----------------------- all the requirements/dependencies
	test.php ------------------------------- Main script




************************************************************************************************************************

Assumptions:

	1. When 2 users have the same score, the newer one gets higher rank (It motivates the other guy to play harder XD).
	2. LeaderboardGet Doesn't update or create data in database when Leaderboard is not in the database.
	3. Assume never deletes Data.

************************************************************************************************************************




Bonus Question Document:

	Reset

	Endpoint: http://{hostname}/Reset

	reset database and all tables to be default.

	Input: A POST request to /Reset with Administrator username and password:
		{
			"Username": <string>,
			"Password": <string>
		}
		
	Output: Return success if database is reset, else will throw error:
		{"Success":true}





************************************************************************************************************************

Improvements:

	1. Further encapsulate database transactions. Get rid of all 'magic queries/arrays' and replace them with functions.
	2. Add more build-in query features like 'inner join' etc..
	3. Althought web ui is not required for this assessment, it's still nice to have a nice documentation page or interface.
	4. Create more classes/functions for frequently used code pieces.
	5. Make DataAccessObject and map all data.
	6. Improve sercurity.

************************************************************************************************************************

Few words in the end:

	Thanks again for the awesome experience! This is a very interesting and fun test that makes me feel like I'm 
	actually making a backend system for a game! I learned a lot just finishing this assessment along. I am so excited
	and proud of the outcome. Cheers!