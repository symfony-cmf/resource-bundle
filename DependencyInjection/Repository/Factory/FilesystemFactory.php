<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\Definition;
use Puli\Repository\FilesystemRepository;

class FilesystemFactory implements RepositoryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $options)
    {
        return new Definition(FilesystemRepository::class, [
            $options['base_dir'],
            $options['symlink'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filesystem';
    }

    /**
     * {@inheritdoc}
     */
    public function configure(OptionsResolver $options)
    {
        $options->setRequired('base_dir');
        $options->setDefault('symlink', true);
    }
}
