<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceBundle\Tests\Functional;

class PhpcrOdmRepositoryTest extends PhpcrRepositoryTestCase
{
    protected function getRepository()
    {
        return $this->repositoryRegistry->get('test_repository_phpcr_odm');
    }
}
