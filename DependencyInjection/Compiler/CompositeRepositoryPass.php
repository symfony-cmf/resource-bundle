<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2016 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class CompositeRepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $taggedRepoIds = $container->findTaggedServiceIds('cmf_resource.repository');

        $map = [];
        foreach ($taggedRepoIds as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new InvalidArgumentException(sprintf(
                    'Resource Repository "%s" has no "alias" attribute in its tag',
                    $id
                ));
            }

            $name = $attributes[0]['alias'];
            $map[$name] = $id;
        }

        foreach ($taggedRepoIds as $id => $attributes) {
            if (!isset($attributes[0]['type'])) {
                continue;
            }

            if ($attributes[0]['type'] !== 'composite') {
                continue;
            }

            $definition = $container->getDefinition($id);
            $newMethodCalls = [];

            // the second argument to mount is a Repository but earlier
            // we populated with the ID of the service we want. Now it
            // should be replaced with a Reference.
            foreach ($definition->getMethodCalls() as $methodCall) {
                $repositoryId = $methodCall[1][1];

                if (array_key_exists($repositoryId, $map)) {
                    // if the ID is a registered alias
                    $reference = new Reference($map[$repositoryId]);
                } else {
                    // else assume it is the ID of a service
                    $reference = new Reference($repositoryId);
                }

                $methodCall[1][1] = $reference;
                $newMethodCalls[] = $methodCall;
            }

            $definition->setMethodCalls($newMethodCalls);
        }
    }
}
