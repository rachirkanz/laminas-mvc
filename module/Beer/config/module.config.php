<?php

namespace Beer;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

$provider = new ConfigProvider();

return [
    'service_manager' => $provider->getDependencyConfig(),
    'view_manager' => $provider->getViewManagerConfig(),
    'beer' => $provider->getModuleConfig(),
    'controllers' => [
        'factories' => [
        ],
    ],
    'navigation' => [
        'manage' => [
            [
                'label'  => 'Beers',
                'route'  => 'manage/beer',
            ],
            [
                'label'  => 'Brewery',
                'route'  => 'manage/brewery',
            ],
            [
                'label'  => 'Category',
                'route'  => 'manage/category',
            ],
            [
                'label'  => 'Style',
                'route'  => 'manage/style',
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\BeerController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'manage' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/manage',
                    'defaults' => [
                        'controller' => Controller\BeerController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'beer' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route' => '/beer[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\BeerController::class,
                                'action' => 'index',
                            ],
                        ]
                    ],
                    'style' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route' => '/style[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\StyleController::class,
                                'action' => 'index',
                            ],
                        ]
                    ],
                    'category' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route' => '/category[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\CategoryController::class,
                                'action' => 'index',
                            ],
                        ]
                    ],
                    'brewery' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route' => '/brewery[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\BreweryController::class,
                                'action' => 'index',
                            ],
                        ]
                    ],
                ]
            ],
        ],
    ],
];
