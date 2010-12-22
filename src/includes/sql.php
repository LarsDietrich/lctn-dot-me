<?php

class Sql {

	/**
	 * Generic function to execute a query and return the results.
	 *
	 * @param $query - the query to execute.
	 */
	function execute($query) {
		require ("configure.php");
		$connection = mysql_connect($server, $username, $password) or die("FAILURE - Couldn't make connection to local database.");
		$db = mysql_select_db($database, $connection) or exit("FAILURE - Error code: " . mysql_errno($connection) . " Error: " . mysql_error($connection)  . ".");
		$query_result = mysql_query($query, $connection) or exit("<hr>code: " . mysql_errno($connection) . " error: " . mysql_error($connection)  . ".");
		return $query_result;
	}
	
}

?>