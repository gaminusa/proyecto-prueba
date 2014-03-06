<?php
namespace Application\Model;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class PromoGanador extends TableGateway {

    protected $adapater;

    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null) {
        $this->adapater = $adapter;
        return parent::__construct('default_promo_ganadores', $adapter, $databaseSchema, $selectResultPrototype);
    }
     
    public function get(){
        return $this->select()->toArray();
    }
    
    public function dniDuplicado($dni) {
        $sql = new Sql($this->adapater);
        $select = $sql->select();
        $select->from(array('gan' => 'default_promo_ganadores'))
                ->join(array('par' => 'default_promo_participants'), 'par.id = gan.participant_id')
                ->join(array('us' => 'default_users'), 'us.id = par.user_id')
                ->join(array('pro' => 'default_profiles'), 'pro.id = us.id',array('pro.name' => 'first_name','pro.last_name'=>'last_name'))
                ->where(
                        array(
                            'pro.dni = "' . $dni . '"'
                        )
        );
        $statement = $sql->prepareStatementForSqlObject($select);
        $statement = $this->adapter->query($statement->getSql());
        $results = $statement->execute();
        return $results->current();
    }

}
