<?php
namespace Beer\Navigation\Service;

use Laminas\Navigation\Service\DefaultNavigationFactory;

/**
 * Factory for the ZfcAdmin admin navigation
 *
 * @package    ZfcAdmin
 * @subpackage Navigation\Service
 */
class BeerNavigationFactory extends DefaultNavigationFactory
{
    /**
     * @{inheritdoc}
     */
    protected function getName()
    {
        return 'manage';
    }
}
