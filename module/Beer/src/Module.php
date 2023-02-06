<?php

namespace Beer;

use Laminas\EventManager\EventInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\V2RouteMatch;
use Laminas\Router\RouteMatch as V3RouteMatch;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\CategoryTable::class => function($container) {
                    $tableGateway = $container->get(Model\CategoryTableGateway::class);
                    return new Model\CategoryTable($tableGateway);
                },
                Model\CategoryTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Category());
                    return new TableGateway('categories', $dbAdapter, null, $resultSetPrototype);
                },
                Model\StyleTable::class => function($container) {
                    $tableGateway = $container->get(Model\StyleTableGateway::class);
                    return new Model\StyleTable($tableGateway);
                },
                Model\StyleTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Style());
                    return new TableGateway('styles', $dbAdapter, null, $resultSetPrototype);
                },
                Model\BreweryTable::class => function($container) {
                    $tableGateway = $container->get(Model\BreweryTableGateway::class);
                    return new Model\BreweryTable($tableGateway);
                },
                Model\BreweryTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Brewery());
                    return new TableGateway('breweries', $dbAdapter, null, $resultSetPrototype);
                },
                Model\BeerTable::class => function($container) {
                    $tableGateway = $container->get(Model\BeerTableGateway::class);
                    return new Model\BeerTable($tableGateway);
                },
                Model\BeerTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Beer());
                    return new TableGateway('beers', $dbAdapter, null, $resultSetPrototype);
                },
                        
            ],
        ];
    }
    
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\CategoryController::class => function($container) {
                    return new Controller\CategoryController(
                        $container->get(Model\CategoryTable::class)
                    );
                },
                Controller\StyleController::class => function($container) {
                    return new Controller\StyleController(
                        $container->get(Model\StyleTable::class)
                    );
                },
                Controller\BreweryController::class => function($container) {
                    return new Controller\BreweryController(
                        $container->get(Model\BreweryTable::class)
                    );
                },
                Controller\BeerController::class => function($container) {
                    return new Controller\BeerController(
                        $container->get(Model\BeerTable::class),
                        $container->get(Model\CategoryTable::class),
                        $container->get(Model\StyleTable::class),
                        $container->get(Model\BreweryTable::class)
                    );
                },
            ],
        ];
    }
    
    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getParam('application');
        $em = $app->getEventManager();
        $em->attach(MvcEvent::EVENT_DISPATCH, [$this, 'selectLayoutBasedOnRoute']);
    }

    public function selectLayoutBasedOnRoute(MvcEvent $e)
    {
        $app = $e->getParam('application');
        $sm = $app->getServiceManager();
        $config = $sm->get('config');

        if ($config['beer']['use_beer_layout'] === false) {
            return;
        }
        $match = $e->getRouteMatch();
        $controller = $e->getTarget();

        if (!($match instanceof V2RouteMatch || $match instanceof V3RouteMatch)
            || 0 !== strpos($match->getMatchedRouteName(), 'manage')
            || ($controller->getEvent()->getResult() && $controller->getEvent()->getResult()->terminate())
        ) {
            return;
        }
        $layout = $config['beer']['beer_layout_template'];
        $controller->layout($layout);
    }
}
