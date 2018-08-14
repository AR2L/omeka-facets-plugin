<?php

class FacetsController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
    	$this->_helper->db->setDefaultModelName('Facets');
    }

    //Affichage du bloc Facets
    public function indexAction() {

    }

}
