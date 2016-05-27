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

interface EnhancerFactoryInterface
{
    /**
     * @return Description
     */
    public function create();
}
