<?php
namespace Application\Model;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class TemporalChapa extends TableGateway {

    protected $adapater;

    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null) {
        $this->adapater = $adapter;
        return parent::__construct('default_temporal_chapas2', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    
    public function save($form){
        $data = array(
            'nombre'            => $form['nombre'],
            'apellidos'         => $form['apellidos'],
            'dni'               => $form['dni'],
            'fecha_nacimiento'  => $form['fecha_nacimiento'],
            'telefono'          => $form['telefono'],
            'codigo_premio'     => $form['codigo_premiacion'],
            'email'             => $form['email'],
            'agencia_bbva'      => $form['agencia'],
            'fecha_registro'    => date('Y-m-d H:i:s')
        );
            return $this->insert($data);
    }
    
    public function get(){
        return $this->select()->toArray();
    }
    
    public function duplicado($codigo){
        return $this->select(array('codigo_premio' => $codigo))->current();
    }
    
    public function dniDuplica($dni){
        return $this->select(array('dni' => $dni))->current();
        
    }

}
