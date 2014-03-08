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
            'claimed' =>$claimed+1);
        
        $sql = new Sql($this->adapater);
        $update = $sql->update();
        $update->table('default_promo_chicos');
        $update->set($data);
        $update->where(array('id' => 1));
        $statement = $sql->prepareStatementForSqlObject($update);        
        $statement->execute();     
    }
    
    public function verificarStock(){
        $prom = $this->get();
        $quanti = $prom['quantity'];
        $claimed = $prom['claimed'];
            if($quanti>$claimed){
                return 1;
            }else{
                return 0;
            }
    }

}
