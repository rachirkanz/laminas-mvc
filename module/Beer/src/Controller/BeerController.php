<?php

namespace Beer\Controller;

use Beer\Form\BeerForm;
use Beer\Model\Beer;
use Beer\Model\BeerTable;
use Beer\Model\CategoryTable;
use Beer\Model\StyleTable;
use Beer\Model\BreweryTable;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container as SessionContainer;
use Laminas\Validator\Csrf as CsrfValidator;

class BeerController extends AbstractController
{
    private $table, $categoryTable, $styleTable, $breweryTable;
    
    public function __construct(BeerTable $table, CategoryTable $categoryTable, StyleTable $styleTable, BreweryTable $breweryTable)
    {
        $this->table = $table;
        $this->categoryTable = $categoryTable;
        $this->styleTable = $styleTable;
        $this->breweryTable = $breweryTable;
    }
    
    public function indexAction()
    {
        // Grab the paginator from the BeerTable:
        $paginator = $this->table->getAll(true);

        // Set the current page to what has been passed in query string,
        // or to 1 if none is set, or the page is invalid:
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);

        // Set the number of items per page to 10:
        $paginator->setItemCountPerPage(10);

        return new ViewModel(['beers' => $paginator]);
    }
    
    public function addAction()
    {
        $form = new BeerForm('beer', $this->categoryTable->getCategoryList(), $this->styleTable->getStyleList(), $this->breweryTable->getBreweryList());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $beer = new Beer();
        $form->setInputFilter($beer->getInputFilter());
        
        $data = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        // Pass data to form.
        $form->setData($data);
        
        $session = new SessionContainer();
        $validator = new CsrfValidator([
            'session' => $session,
        ]);
        $hash = $validator->getHash();
        
        if(!$validator->isValid($hash)) {
            $this->flashMessenger()->addErrorMessage('Invalid request.');
            return ['form' => $form];
        }

        if (! $form->isValid()) {
            $this->flashMessenger()->addErrorMessage('Invalid request.');
            return [
                'form' => $form,
                'messages' => $form->getMessages()
            ];
        }

        $beer->exchangeArray($form->getData());
        $this->table->saveBeer($beer);
        return $this->redirect()->toRoute('manage/beer');
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('manage/beer', ['action' => 'add']);
        }

        // Retrieve the beer with the specified id. Doing so raises
        // an exception if the beer is not found, which should result
        // in redirecting to the landing page.
        try {
            $beer = $this->table->getBeer($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('manage/beer', ['action' => 'index']);
        }

        $form = new BeerForm('beer', $this->categoryTable->getCategoryList(), $this->styleTable->getStyleList(), $this->breweryTable->getBreweryList());
        $form->bind($beer);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form, 'beerObj' => $beer];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($beer->getInputFilter());
        $data = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        // Pass data to form.
        $form->setData($data);

        $session = new SessionContainer();
        $validator = new CsrfValidator([
            'session' => $session,
        ]);
        $hash = $validator->getHash();
        
        if(!$validator->isValid($hash)) {
            $this->flashMessenger()->addErrorMessage('Invalid request.');
            return ['form' => $form];
        }
        
        if (! $form->isValid()) {
            $this->flashMessenger()->addErrorMessage('Invalid request.');
            $viewData['messages'] = $form->getMessages();
            return $viewData;
        }

        try {
            $this->table->saveBeer($beer);
        } catch (\Exception $e) {
        }

        // Redirect to beer list
        return $this->redirect()->toRoute('manage/beer', ['action' => 'index']);
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('manage/beer');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteBeer($id);
            }

            // Redirect to list of beers
            return $this->redirect()->toRoute('manage/beer');
        }

        return [
            'id'    => $id,
            'beer' => $this->table->getBeer($id),
        ];
    }
}
