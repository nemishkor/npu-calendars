<?php
require_once __DIR__ . '/google/vendor/autoload.php';
require_once __DIR__ . '/google/src/Google/Client.php';
require_once __DIR__ . '/google/vendor/google/apiclient-services/Google/Service/Calendar.php';
require_once __DIR__ . '/model.php';
 
class Google extends Model{
	
	public $client;
	public $redirect_uri;
	public $user_data;
	
	function __construct($registry, $table_name){
		parent::__construct($registry, $table_name);
		session_start();
		$this->client = new Google_Client();
		$this->client->setApplicationName("NewPage Calendars");
		$this->client->setDeveloperKey("AIzaSyCnoWnA1tiP9ke7LwqlcXvbx3BRYnuv9jI");
		$this->client->setClientId('712448737249-etsol9rup6n05dnpamqq5rgsnlmq90kh.apps.googleusercontent.com');
		$this->client->setClientSecret('UsDxl_J2KQIztYcS8YCLqMYf');
		$this->client->addScope('email');
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
		$this->client->setAccessType('offline');
		$this->client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/user/oauth2callback');
		if (!empty($_SESSION['access_token']) && isset($_SESSION['access_token']['id_token'])) {
			$this->client->setAccessToken($_SESSION['access_token']);
			$this->user_data = $this->client->verifyIdToken();
		} else {
			if($this->registry['controller_name'] != 'user') {
				$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/user/index';
				header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
			}
		}
	}

	function get_user(){
		if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
			$access_token = $_SESSION['access_token'];
			$query = "SELECT * FROM {$this->table_name} WHERE hash='{$access_token['access_token']}'";
			$result = $this->db->query($query);
			$user = $result->fetch_assoc();
			return $user;
		} else {
			return false;
		}
	}
	
	function get_user_permissions($user_id){
		$result = $this->db->query("SELECT g.permissions FROM users_groups g INNER JOIN {$this->table_name} u ON u.group_id = g.id WHERE u.id='{$user_id}'");
		if($result->num_rows){
			$row = $result->fetch_array();
			if($row['permissions']){
				return json_decode($row['permissions']);
			}
		}
		return false;
	}
	
	function set_permissions($group_id, $values){
		$result = $this->db->query("SELECT * FROM users_groups WHERE id='{$group_id}'");
		if($result->num_rows){
			$group = $result->fetch_assoc();
			if($group){
				$permissions = json_decode($group['permissions']);
				foreach($values as $name=>$value){
					$permissions->$name = $value;
				}
				$permissions_json = json_encode($permissions);
				$query = $this->db->query("UPDATE users_groups SET permissions='{$permissions_json}' WHERE id='{$group_id}'");
				if($query)
					return true;
			}
		}
		return false;
	}
	
	function get_permisison($permission){
		$user = $this->get_user();
		if($user){
			$permissions = $this->get_user_permissions($user['id']);
			if($permissions && isset($permissions->$permission)){
				return $permissions->$permission;
			}
		}
		return false;
	}
	
	function get_permission($permission_request){
		// check permissions
		$user = $this->get_user();
		if($user){ // if user authenticated get current user permission
			$permissions = $this->get_user_permissions($user['id']);
			$group_id = $user['group_id'];
		} else { // else get guest permissions
			$group_id = 4;
			$permissions = $this->get_group_permissions($group_id);
		}
		if($permissions && isset($permissions->$permission_request)){
			if($permissions->$permission_request){ // if access allowed run action with getting data from model if it need
				return true;
			} else { // if access denied just show view without data
				$this->registry->set('error', 'Access denied. You cannot view this content =/');
			    return false;
			}
		} else {
			$this->set_permissions($group_id, array($permission_request => '0'));
			$this->registry->set('error', 'Cannot get permissions. Try again please. $permissions = false || $permissions = NULL.');
			return false;
		}
	}

	function check_permission(){
		return $this->get_permission($this->registry['controller_name'] . '_' . $this->registry['action_name']);
	}
	
	function get_group_permissions($group_id){
		$result = $this->db->query("SELECT permissions FROM users_groups WHERE id='{$group_id}'");
		if($result->num_rows){
			$row = $result->fetch_array();
			if($row['permissions']){
				return json_decode($row['permissions']);
			}
		}
		return false;
	}
	
	function addCalendar($id = null){
		//~ if(!$id)
			//~ return false;
		$data = array();
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			$this->client->setScopes(array(Google_Service_Calendar::CALENDAR));
			$service = new Google_Service_Calendar($this->client);
			$event = new Google_Service_Calendar_Event(array(
			  'summary' => 'Google I/O 2015',
			  'location' => '800 Howard St., San Francisco, CA 94103',
			  'description' => 'A chance to hear more about Google\'s developer products.',
			  'start' => array(
				'dateTime' => '2015-05-28T09:00:00-07:00',
				'timeZone' => 'America/Los_Angeles',
			  ),
			  'end' => array(
				'dateTime' => '2015-05-28T17:00:00-07:00',
				'timeZone' => 'America/Los_Angeles',
			  ),
			  'recurrence' => array(
				'RRULE:FREQ=DAILY;COUNT=2'
			  ),
			  'attendees' => array(
				array('email' => 'lpage@example.com'),
				array('email' => 'sbrin@example.com'),
			  ),
			  'reminders' => array(
				'useDefault' => FALSE,
				'overrides' => array(
				  array('method' => 'email', 'minutes' => 24 * 60),
				  array('method' => 'popup', 'minutes' => 10),
				),
			  ),
			));

			$calendarId = 'primary';
			$event = $service->events->insert($calendarId, $event);
//			$data("Event created:", $event->htmlLink);
		} else {
		    $data['authUrl'] = $this->client->createAuthUrl();
		}
		return $data;
	}
	
}
?>
