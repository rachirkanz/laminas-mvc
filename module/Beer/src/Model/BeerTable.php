<?php

namespace Beer\Model;

use RuntimeException;
use Laminas\Db\Sql\Select;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;

class BeerTable  extends AbstractTable
{
    public function getAll($paginated = false)
    {
        if ($paginated) {
            return $this->getPaginatedResults();
        }

        return $this->tableGateway->select();
    }

    private function getPaginatedResults()
    {
        // Create a new Select object for the table:
        $select = new Select($this->tableGateway->getTable());
        $select->join(array('c' => 'categories'), 'c.id = beers.cat_id', ['category_name' => 'name'], 'left');
        $select->join(array('s' => 'styles'), 's.id = beers.style_id', ['style_name' => 'name'], 'left');
        $select->join(array('b' => 'breweries'), 'b.id = beers.brewery_id', ['brewery_name' => 'name'], 'left');

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
    
    public function getBeer($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveBeer(Beer $beer)
    {
        $data = [
            'name' => $beer->name,
            'brewery_id' => $beer->brewery_id,
            'cat_id' => $beer->cat_id,
            'style_id' => $beer->style_id,
            'abv' => $beer->abv,
            'ibu' => $beer->ibu,
            'srm' => $beer->srm,
            'upc' => $beer->upc,
            'filepath' => $beer->filepath['name'],
            'descript' => $beer->descript,
            'add_user' => 0, // For now static @ToDo Update dynamic value later
        ];

        $id = (int) $beer->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getBeer($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update beer with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteBeer($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}