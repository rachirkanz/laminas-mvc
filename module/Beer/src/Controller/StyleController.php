<?php

namespace Beer\Controller;

use Beer\Form\StyleForm;
use Beer\Model\Style;
use Beer\Model\StyleTable;
use Laminas\View\Model\ViewModel;

class StyleController extends AbstractController
{
    private $table;
    
    public function __construct(StyleTable $table)
    {
        $this->table = $table;
    }
    
    public function indexAction()
    {
        // Grab the paginator from the StyleTable:
        $paginator = $this->table->fetchAll(true);

        // Set the current page to what has been passed in query string,
        // or to 1 if none is set, or the page is invalid:
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);

        // Set the number of items per page to 10:
        $paginator->setItemCountPerPage(10);

        return new ViewModel(['styles' => $paginator]);
    }
    
    public function addAction()
    {
        $form = new StyleForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $style = new Style();
        $form->setInputFilter($style->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $style->exchangeArray($form->getData());
        $this->table->saveStyle($style);
        return $this->redirect()->toRoute('manage/style');
    }
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('manage/style', ['action' => 'add']);
        }

        // Retrieve the style with the specified id. Doing so raises
        // an exception if the style is not found, which should result
        // in redirecting to the landing page.
        try {
            $style = $this->table->getStyle($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('manage/style', ['action' => 'index']);
        }

        $form = new StyleForm();
        $form->bind($style);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($style->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        try {
            $this->table->saveStyle($style);
        } catch (\Exception $e) {
        }

        // Redirect to style list
        return $this->redirect()->toRoute('manage/style', ['action' => 'index']);
    }
    
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('manage/style');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteStyle($id);
            }

            // Redirect to list of styles
            return $this->redirect()->toRoute('manage/style');
        }

        return [
            'id'    => $id,
            'style' => $this->table->getStyle($id),
        ];
    }
}
