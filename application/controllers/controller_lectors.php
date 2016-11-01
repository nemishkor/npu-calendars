<?php
class Controller_Lectors extends Crud_Controller{	

    public function action_import(){
        $this->view->generate("lectors_import_view.php");
    }

    public function action_import_ajax(){
        $new_ids = [];
        foreach ($_POST['data'] as $item){
            $new_ids[] = $this->model->save_obj($item);
        }
        echo $new_ids;
    }

}
?>
