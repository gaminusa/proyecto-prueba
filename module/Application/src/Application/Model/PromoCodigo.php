<?php
namespace Application\Model;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class PromoCodigo extends TableGateway {

    protected $adapater;

    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null) {
        $this->adapater = $adapter;
        return parent::__construct('default_promo_codigos', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function get(){
        return $this->select()->toArray();
    }
    
    public function validarCodigo($codigo){
        $sql = new Sql($this->adapater);
        $select = $sql->select();
        $select->columns(array('code' => 'code'));
        $select->from(array('codigo' => 'default_promo_codigos'))
                ->where(
                        array(
                            'codigo.claimed= 0',
                            'codigo.code = "' . $codigo .'"'
                        )
        );
        $statement = $sql->prepareStatementForSqlObject($select);
        $statement = $this->adapter->query($statement->getSql());
        $results = $statement->execute()->current();
        
        return $results;
    }
    
    public function updateClaimed($codigo){
        
        $data = array(
            'claimed' =>1);
        
        $sql = new Sql($this->adapater);
        $update = $sql->update();
        $update->table('default_promo_codigos');
        $update->set($data);
        $update->where(array('code' => $codigo));
        $statement = $sql->prepareStatementForSqlObject($update);        
        $statement->execute();     
    }
}
