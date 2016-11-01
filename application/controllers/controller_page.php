<?php
class Controller_Page extends Controller{

    function action_index()
    {
        $this->action_home();
    }

    function action_home(){
        $page = $this->registry->get('action_name');
        if($page == 'index')
            $page = 'home';
        $this->view->generate('pages/' . $page . '.php', 'template_full_screen_view.php');
    }

    function action_changelog(){
        $this->view->generate('pages/changelog.php', 'template_view.php');
    }

}