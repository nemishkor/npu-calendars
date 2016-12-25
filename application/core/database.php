<?php
class Database {
	
	static public function getDb(){
		return new mysqli("localhost", "nemis206_schedul", "6LW{[!h_zJ?D", "nemis206_schedules");
	}
	
}
?>
