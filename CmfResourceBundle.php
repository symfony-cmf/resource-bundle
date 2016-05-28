<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\FilesystemFactory;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\DoctrinePhpcrFactory;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\DoctrinePhpcrOdmFactory;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler\DescriptionEnhancerPass;

class CmfResourceBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $extension = $container->getExtension('cmf_resource');
        $extension->addRepositoryFactory('filesystem', new FilesystemFactory());
        $extension->addRepositoryFactory('doctrine_phpcr', new DoctrinePhpcrFactory());
        $extension->addRepositoryFactory('doctrine_phpcr_odm', new DoctrinePhpcrOdmFactory());

        $container->addCompilerPass(new DescriptionEnhancerPass());

        parent::build($container);
    }
}
