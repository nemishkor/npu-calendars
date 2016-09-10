<?php
class Controller_Lectors extends Crud_Controller{	

    public function action_import(){
        $this->view->generate("lectors_import_view.php");
    }

}
?>
