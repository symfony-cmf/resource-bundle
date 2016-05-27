<?php
/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Description\Enhancer\Factory;

use Symfony\Cmf\Component\Resource\Description\Enhancer\Sonata\AdminEnhancer;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Description\Enhancer\Factory\EnhancerFactoryInterface;

class SonataAdminFactory implements EnhancerFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new Definition(AdminEnhancer::class, [
            new Reference('sonata.admin.pool'),
            new Reference('router')
        ]);
    }
}
