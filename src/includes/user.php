<?php 
require_once 'sql.php';

class User {
	
	public $foursquare_token;
	public $facebook_id;
	public $id;
	
	function __construct($facebook) {
		$this->facebook_id = $facebook;
		$sql = new Sql();
		$query = "SELECT id, facebook, foursquare FROM USER WHERE facebook = " . $facebook;
		$result = mysql_fetch_assoc($sql->execute($query));

		if (strlen($result["facebook"]) = 0) {
			$query = "INSERT user (facebook, foursquare) VALUES (" . $facebook . ", '')";
			$sql->execute($query);			
		} else {
			$this->id = $result["id"];		
			if (strlen($result["foursquare"]) > 0) {
				$this->foursquare_token = $result["foursquare"];
			}
		}
	}
}
?>