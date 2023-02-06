<?php
namespace Beer\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;

class AbstractController extends AbstractActionController 
{
    protected $baseUrl;
    
    public function onDispatch(MvcEvent $e) {
        $this->baseUrl = $this->getRequest()->getBasePath();
       
        $action = $e->getRouteMatch()->getParam('action', 'index');
        $e->getTarget()->layout()->action = $action;
        
        $e->getViewModel()->setVariable('footerContent', '@2023 Laminas - Beers Management');
        
        return parent::onDispatch($e);
    }

}