<?php

namespace Beer;

class ConfigProvider
{
    /**
     * Provide dependency configuration for an application integrating i18n.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'view_manager' => $this->getViewManagerConfig(),
            'beer' => $this->getModuleConfig(),
        ];
    }
    /**
     * Provide dependency configuration for an application.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'factories' => [
                'beer_navigation' => Navigation\Service\BeerNavigationFactory::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getViewManagerConfig()
    {
        return [
            'template_path_stack' => [
                __DIR__ . '/../view',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getModuleConfig()
    {
        return [
            'use_beer_layout' => true,
            'beer_layout_template' => 'layout/beers',
        ];
    }
}
