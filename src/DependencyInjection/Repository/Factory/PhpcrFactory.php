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

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Cmf\Component\Resource\Repository\PhpcrRepository;

class PhpcrFactory implements RepositoryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $options)
    {
        return new Definition(PhpcrRepository::class, [
            new Reference('doctrine_phpcr.session'),
            $options['basepath'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phpcr/phpcr';
    }

    /**
     * {@inheritdoc}
     */
    public function configure(OptionsResolver $options)
    {
        $options->setDefault('basepath', null);
    }
}
