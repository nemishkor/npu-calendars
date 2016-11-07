<?php
class Database {
	
	static public function getDb(){
		return new mysqli("localhost", "", "", "");
	}
	
}
?>
