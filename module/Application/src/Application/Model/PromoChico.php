<?php
namespace Application\Model;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class PromoChico extends TableGateway {

    protected $adapater;

    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null) {
        $this->adapater = $adapter;
        return parent::__construct('default_promo_chicos', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function get(){
        return $this->select(array('id'=>1))->current();
    }
    
    public function updateClaimed() {
        $prom = $this->get();
        $claimed = $prom['claimed'];
        
        $data = array(
            'claimed' => $claimed+1
        );
        
        $this->update($data);
    }

}
