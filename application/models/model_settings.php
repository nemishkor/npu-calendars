<?php
class Model_Settings extends Model
{
	
	function __construct($registry){
		parent::__construct($registry);
	}
	
	public function get_data()
	{
		$result = $this->db->query("SELECT * FROM `Settings`");
		return $result;
	}
}
?>
