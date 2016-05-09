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
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Puli\Repository\CompositeRepository;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\CmfResourceExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class CompositeFactory implements RepositoryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $options)
    {
        $definition = new Definition(CompositeRepository::class);

        foreach ($options['mounts'] as $mountConfig) {
            $definition->addMethodCall(
                'mount',
                [
                    $mountConfig['mountpoint'],
                    new Reference(
                        CmfResourceExtension::getRepositoryServiceId($mountConfig['repository'])
                    ),
                ]
            );
        }

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'composite';
    }

    /**
     * {@inheritdoc}
     */
    public function configure(OptionsResolver $options)
    {
        $options->setDefault('mounts', []);
        $options->setAllowedTypes('mounts', 'array');
        $options->setNormalizer('mounts', function (Options $options, $value) {
            if (empty($value)) {
                return $value;
            }

            foreach ($value as $mountConfig) {
                $allowed = ['mountpoint', 'repository'];

                foreach ($allowed as $key) {
                    if (!isset($mountConfig[$key])) {
                        throw new InvalidOptionsException(sprintf(
                            'The "%s" option is required when defining a "%s" repository.',
                            $key,
                            $this->getName()
                        ));
                    }
                }

                if ($diff = array_diff(array_keys($mountConfig), $allowed)) {
                    throw new InvalidOptionsException(sprintf(
                        'The options "%s" are not allowed when defining a "%s" repository.',
                        implode('", "', $diff),
                        $this->getName()
                    ));
                }
            }

            return $value;
        });
    }
}
