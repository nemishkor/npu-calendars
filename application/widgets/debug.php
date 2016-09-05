<?php
/**
 * Copyright (c) 2016. Vitaliy Korenev (http://newpage.in.ua)
 */
include_once "application/models/model_user.php";

class Widget_Debug extends Widget{

    function display(){
        $model = new Model_User($this->registry);
        $user = $model->get_user();
        if(empty($user) || !$user['group_id'] != 1)
            return;
		if(!empty($this->registry['debug'])){
            echo '<span class="uk-margin-large-top uk-text-muted">debug</span>';
            echo '<pre>';
            var_dump($this->registry['debug']);
            echo '</pre>';
        }
		if(!empty($data)){
            ?>
            <span class="uk-margin-large-top uk-text-muted">var_dump($data);</span>
            <pre>
			<?php
            var_dump($data);
            ?>
			</pre>
            <?php
        }
    }

}
?>
