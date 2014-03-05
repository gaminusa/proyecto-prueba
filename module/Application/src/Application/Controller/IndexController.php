<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\TemporalChapa;


class IndexController extends AbstractActionController
{
    
    public $dbAdapter;
    

    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function saveAction() {
       $form = $this->getRequest()->getPost('form');
       print_r("aa");
       print_r($form);
       die();
        if ($this->getRequest()->isPost()) {
            print_r("entro");die();
            $form = $this->getRequest()->getPost('form');
            $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $o_chapas = new TemporalChapa($this->dbAdapter);
            $o_chapas->save($form);
            
            return $this->getResponse()->setContent(json_encode(array('value' => 1)));
        }

        return $this->getResponse()->setContent(json_encode(array('value' => 0)));
    }

}
