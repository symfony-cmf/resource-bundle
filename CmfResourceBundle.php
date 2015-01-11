<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler\RegistryPass;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler\CompositeRepositoryPass;

class CmfResourceBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegistryPass());
        $container->addCompilerPass(new CompositeRepositoryPass());
        parent::build($container);
    }
}
