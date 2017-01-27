<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Twig;

use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;
use Symfony\Cmf\Component\Resource\Description\DescriptionFactory;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class DescriptionExtension extends \Twig_Extension
{
    private $descriptionFactory;

    public function __construct(DescriptionFactory $descriptionFactory)
    {
        $this->descriptionFactory = $descriptionFactory;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cmf_resource_description', [$this, 'getDescription']),
        ];
    }

    public function getDescription(PuliResource $resource)
    {
        return $this->descriptionFactory->getPayloadDescriptionFor($resource);
    }

    public function getName()
    {
        return 'cmf_resource_description';
    }
}
