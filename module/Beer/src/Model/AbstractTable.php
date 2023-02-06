<?php

namespace Beer\Model;

use Laminas\Db\TableGateway\TableGatewayInterface;
use Application\Model\Rowset\AbstractModel;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Db\Sql\Select;

class AbstractTable
{
    protected $tableGateway;
        
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($paginated = false)
    {
        if ($paginated) {
            return $this->fetchPaginatedResults();
        }

        return $this->tableGateway->select();
    }

    private function fetchPaginatedResults()
    {
        // Create a new Select object for the table:
        $select = new Select($this->tableGateway->getTable());

        // Create a new result set based on the Album entity:
        //$resultSetPrototype = new ResultSet();
        //$resultSetPrototype->setArrayObjectPrototype(new Album());

        // Create a new pagination adapter object:
        $paginatorAdapter = new DbSelect(
            // our configured select object:
            $select,
            // the adapter to run it against:
            $this->tableGateway->getAdapter(),
            // the result set to hydrate:
            $this->tableGateway->getResultSetPrototype()
        );

        return new Paginator($paginatorAdapter);
    }

    protected function fetchRow($passedSelect)
    {
        $row = $this->tableGateway->selectWith($passedSelect);
        return $row->current();
    }
    
    public function saveRow(AbstractModel $userModel, $data = null)
    {
        $id = $userModel->getId();
        //if the parameter $data is not passed in, then update all of the object’s properties
        if (empty($data)) {
            $data = $userModel->getArrayCopy();
        }
        if (empty($id)) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->getLastInsertValue();
        }
        if (!$this->getById($id)) {
            throw new RuntimeException(get_class($userModel) .' with id: '.$id.' not found');
        }
        $this->tableGateway->update($data, ['id' => $id]);
        return $id;
    }
    
    public function deleteRow($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
    
    public function getTableGateway()
    {
        return $this->tableGateway;
    }
}
