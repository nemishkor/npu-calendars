<?php 
class Widget_Login extends Widget{
	
	public function display(){
		$google = $this->registry['google'];
		$user = $google->get_user();
		if(isset($user['name']) && $user['name'])
		    $user_name = $user['name'];
		else
		    $user_name = '(' . $user['email'] . ')';
		if($user){
			echo '<a title="Переглянути та відредагувати профіль" data-uk-tooltip="{pos:\'bottom\'}" href="/user" class="uk-button uk-button-primary" style="margin: 4px 10px 0 0;"><i class="uk-icon-user"></i> Вітаємо ' . $user_name . '</a>';
		} else {
			echo '<a href="' . $link_auth . '" class="uk-button uk-button-primary" style="margin: 4px 10px 0 0;"><i class="uk-icon-user"></i> Увійти</a>';
		}
	}
}
?>
