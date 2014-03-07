<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\TemporalChapa;
use Application\Model\PromoCodigo;
use Application\Model\PromoChico;
use Application\Model\PromoGanador;


class IndexController extends AbstractActionController
{
    
    public $dbAdapter;
    

    public function indexAction()
    {
        
        return new ViewModel();
    }
    
    public function formAction(){
        echo "entro";
        return new ViewModel();
    }
    
    public function saveAction() {
        if ($this->getRequest()->isPost()) {

            $form = $this->getRequest()->getPost('form');
            $codigo = $form['codigo_premiacion'];
            $dni = $form['dni'];
            $validar = $this->validar($codigo);

            if ($validar == 1) {
                $duplicado = $this->validarDuplicado($codigo);
                if ($duplicado == 1) {
                    $d_dni = $this->validarDNI($dni);
                    if ($d_dni == '') {
                        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
                        $o_chapas = new TemporalChapa($this->dbAdapter);
                        $o_promo = new PromoChico($this->dbAdapter);
                        $o_chapas->save($form);
                        $o_promo->updateClaimed();
                        
                        return $this->getResponse()->setContent(json_encode(array('value' => 1)));
                    } else {
                        return $this->getResponse()->setContent(json_encode(array('value' => 3)));
                    }
                } else {
                    return $this->getResponse()->setContent(json_encode(array('value' => 2)));
                }
            } else {
                return $this->getResponse()->setContent(json_encode(array('value' => 0)));
            }
        }
        return $this->getResponse()->setContent(json_encode(array('value' => 'error')));
    }

    private function validar($codigo) {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $o_codigo = new PromoCodigo($this->dbAdapter);
        $cont_codigo = $o_codigo->validarCodigo($codigo);

        return $cont_codigo;
    }
    
    private function validarDuplicado($codigo) {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $o_codigo = new TemporalChapa($this->dbAdapter);
        $cont_codigo = $o_codigo->duplicado($codigo);

        return $cont_codigo;
    }
    
    private function validarDNI($dni){
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $o_dni = new PromoGanador($this->dbAdapter);
        $cont_dni = $o_dni->dniDuplicado($dni);
        
        return $cont_dni;
    }

}
