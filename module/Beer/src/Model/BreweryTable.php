<?php

namespace Beer\Model;

use RuntimeException;

class BreweryTable  extends AbstractTable
{
    public function getBrewery($id)
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

    public function saveBrewery(Brewery $brewery)
    {
        $data = [
            'name' => $brewery->name,
        ];

        $id = (int) $brewery->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getBrewery($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update brewery with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteBrewery($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
    
    public function getBreweryList() {
        $list = [];
        $allBreweries = $this->tableGateway->select();
        
        if ($allBreweries->count()) {
            $list = $allBreweries->toArray();
        }
        
        return $list;
    }
}