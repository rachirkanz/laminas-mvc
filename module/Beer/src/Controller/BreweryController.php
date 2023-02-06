<?php

namespace Beer\Controller;

use Beer\Form\BreweryForm;
use Beer\Model\Brewery;
use Beer\Model\BreweryTable;
use Laminas\View\Model\ViewModel;

class BreweryController extends AbstractController
{
    private $table;
    
    public function __construct(BreweryTable $table)
    {
        $this->table = $table;
    }
    
    public function indexAction()
    {
        // Grab the paginator from the BreweryTable:
        $paginator = $this->table->fetchAll(true);

        // Set the current page to what has been passed in query string,
        // or to 1 if none is set, or the page is invalid:
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);

        // Set the number of items per page to 10:
        $paginator->setItemCountPerPage(10);

        return new ViewModel(['breweries' => $paginator]);
    }
    
    public function addAction()
    {
        $form = new BreweryForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $brewery = new Brewery();
        $form->setInputFilter($brewery->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $brewery->exchangeArray($form->getData());
        $this->table->saveBrewery($brewery);
        
        $this->flashMessenger()->addSuccessMessage('Brewery has been added.');
        
        return $this->redirect()->toRoute('manage/brewery');
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('manage/brewery', ['action' => 'add']);
        }

        // Retrieve the brewery with the specified id. Doing so raises
        // an exception if the brewery is not found, which should result
        // in redirecting to the landing page.
        try {
            $brewery = $this->table->getBrewery($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('manage/brewery', ['action' => 'index']);
        }

        $form = new BreweryForm();
        $form->bind($brewery);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($brewery->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        try {
            $this->table->saveBrewery($brewery);
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage('There was an error in brewery update.');
            return $this->redirect()->toRoute('manage/brewery', ['action' => 'index']);
        }

        $this->flashMessenger()->addSuccessMessage('Brewery has been updated.');
        
        // Redirect to brewery list
        return $this->redirect()->toRoute('manage/brewery', ['action' => 'index']);
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('manage/brewery');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteBrewery($id);
            }

            $this->flashMessenger()->addSuccessMessage('Brewery has been deleted.');
            // Redirect to list of breweries
            return $this->redirect()->toRoute('manage/brewery');
        }

        return [
            'id'    => $id,
            'brewery' => $this->table->getBrewery($id),
        ];
    }
}
