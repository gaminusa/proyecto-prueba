<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\TemporalChapa;
use Application\Model\PromoCodigo;


class IndexController extends AbstractActionController
{
    
    public $dbAdapter;
    

    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function saveAction() {
        if ($this->getRequest()->isPost()) {
            
            $form = $this->getRequest()->getPost('form');
            $codigo = $form['codigo_premiacion'];
            $this->validar($codigo);
            $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            
            $o_chapas = new TemporalChapa($this->dbAdapter);
            
            $o_chapas->save($form);
            
            return $this->getResponse()->setContent(json_encode(array('value' => 1)));
        }

        return $this->getResponse()->setContent(json_encode(array('value' => 0)));
    }
    
        private function validar($codigo){
            $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $o_codigo = new PromoCodigo($this->dbAdapter);
            $cont_codigo = $o_codigo->validarCodigo($codigo);
            print_r($cont_codigo);die();
            
            return 1;
        }

}
