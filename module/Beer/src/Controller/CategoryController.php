<?php

namespace Beer\Controller;

use Beer\Form\CategoryForm;
use Beer\Model\Category;
use Beer\Model\CategoryTable;
use Laminas\View\Model\ViewModel;

class CategoryController extends AbstractController
{
    private $table;
    
    public function __construct(CategoryTable $table)
    {
        $this->table = $table;
    }
    
    public function indexAction()
    {
        // Grab the paginator from the CategoryTable:
        $paginator = $this->table->fetchAll(true);

        // Set the current page to what has been passed in query string,
        // or to 1 if none is set, or the page is invalid:
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);

        // Set the number of items per page to 10:
        $paginator->setItemCountPerPage(10);

        return new ViewModel(['categories' => $paginator]);
    }
    
    public function addAction()
    {
        $form = new CategoryForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $category = new Category();
        $form->setInputFilter($category->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $category->exchangeArray($form->getData());
        $this->table->saveCategory($category);
        return $this->redirect()->toRoute('manage/category');
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('manage/category', ['action' => 'add']);
        }

        // Retrieve the category with the specified id. Doing so raises
        // an exception if the category is not found, which should result
        // in redirecting to the landing page.
        try {
            $category = $this->table->getCategory($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('manage/category', ['action' => 'index']);
        }

        $form = new CategoryForm();
        $form->bind($category);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($category->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        try {
            $this->table->saveCategory($category);
        } catch (\Exception $e) {
        }

        // Redirect to category list
        return $this->redirect()->toRoute('manage/category', ['action' => 'index']);
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('manage/category');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteCategory($id);
            }

            // Redirect to list of categories
            return $this->redirect()->toRoute('manage/category');
        }

        return [
            'id'    => $id,
            'category' => $this->table->getCategory($id),
        ];
    }
}
