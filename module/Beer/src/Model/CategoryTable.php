<?php

namespace Beer\Model;

use RuntimeException;

class CategoryTable  extends AbstractTable
{
    public function getCategory($id)
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

    public function saveCategory(Category $category)
    {
        $data = [
            'name' => $category->name,
        ];

        $id = (int) $category->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getCategory($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update category with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteCategory($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
    
    public function getCategoryList() {
        $list = [];
        $allCategories = $this->tableGateway->select();
        
        if ($allCategories->count()) {
            $list = $allCategories->toArray();
        }
        
        return $list;
    }
}