<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Repository factories enable new repository types to be made available at
 * runtime by adding them to the CmfResourceExtension during the bundle
 * `build()` method.
 *
 * ```php
 *  class AcmeBundle extends Bundle
 *  {
 *      public function build(ContainerBuilder $container)
 *      {
 *          $extension = $container->getExtension('cmf_resource');
 *          $extension->addRepositoryFactory('my_repository_type', new MyRepositoryFactory());
 *      }
 *  }
 * ```
 */
interface RepositoryFactoryInterface
{
    /**
     * Configure a repository service using the given options.
     *
     * @param array $options
     *
     * @return ResourceRepository
     */
    public function create(array $options);

    /**
     * Return the name of the repository, for example `doctrine_phpcr`.
     *
     * @return string
     */
    public function getName();

    /**
     * Configure options.
     *
     * @param OptionsResolver
     */
    public function configure(OptionsResolver $options);
}
