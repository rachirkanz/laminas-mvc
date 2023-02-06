<?php

namespace Beer\Model;

use RuntimeException;

class StyleTable  extends AbstractTable
{
    public function getStyle($id)
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

    public function saveStyle(Style $style)
    {
        $data = [
            'name' => $style->name,
        ];

        $id = (int) $style->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getStyle($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update style with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteStyle($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
    
    public function getStyleList() {
        $list = [];
        $allStyles = $this->tableGateway->select();
        
        if ($allStyles->count()) {
            $list = $allStyles->toArray();
        }
        
        return $list;
    }
}