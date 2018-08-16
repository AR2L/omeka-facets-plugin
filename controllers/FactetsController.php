<?php

// class FacetsController extends \Omeka\Controller\ItemsController
class FacetsController extends Omeka_Controller_AbstractActionController
{
    //Affichage du bloc Facets
    public function browseAction() {

      parent::browseAction();

      // Custom share of current selection Ids
      $params = $this->getAllParams();
      $itemsArray = array();
      $allItems = $this->_helper->db->findBy($params);
      foreach($allItems as $item){
        $itemsArray[] = $item->id;
      }
      $this->view->assign(array(
          'itemsArray' => $itemsArray,
          'queryParams' => $params
        )
      );

      // return \Omeka\Controller\ItemsController::browseAction();
    }

}
