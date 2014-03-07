<?php
namespace Application\Model;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class User extends TableGateway {

    protected $adapater;

    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null) {
        $this->adapater = $adapter;
        return parent::__construct('default_users', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function get(){
        return $this->select()->toArray();
    }
    
    public function validarLoggin($form) {
        $user = $form['user'];
        //$password = base64_decode($form['password']);
        
        $sql = new Sql($this->adapater);
        $select = $sql->select();
        $select->columns(array('username' => 'username'));
        $select->from(array('us' => 'default_users'))
               ->where(
                        array(
                            'us.username = "' . $user .'"'
                        )
        );
               
        $statement = $sql->prepareStatementForSqlObject($select);
        $statement = $this->adapter->query($statement->getSql());
        $results = $statement->execute()->current();
        
        //$results =$this->select(array('username'=>$user))->current();
        print_r(($results));die();
        return $results;
    }

}
