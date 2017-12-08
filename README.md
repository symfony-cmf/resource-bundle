# Symfony CMF Resource Bundle

[![Latest Stable Version](https://poser.pugx.org/symfony-cmf/resource-bundle/v/stable)](https://packagist.org/packages/symfony-cmf/resource-bundle)
[![Latest Unstable Version](https://poser.pugx.org/symfony-cmf/resource-bundle/v/unstable)](https://packagist.org/packages/symfony-cmf/resource-bundle)
[![License](https://poser.pugx.org/symfony-cmf/resource-bundle/license)](https://packagist.org/packages/symfony-cmf/resource-bundle)

[![Total Downloads](https://poser.pugx.org/symfony-cmf/resource-bundle/downloads)](https://packagist.org/packages/symfony-cmf/resource-bundle)
[![Monthly Downloads](https://poser.pugx.org/symfony-cmf/resource-bundle/d/monthly)](https://packagist.org/packages/symfony-cmf/resource-bundle)
[![Daily Downloads](https://poser.pugx.org/symfony-cmf/resource-bundle/d/daily)](https://packagist.org/packages/symfony-cmf/resource-bundle)

Branch | Travis | Coveralls |
------ | ------ | --------- |
master | [![Build Status][travis_unstable_badge]][travis_link] | [![Coverage Status][coveralls_unstable_badge]][coveralls_unstable_link] |

This package is part of the [Symfony Content Management Framework (CMF)](http://cmf.symfony.com/) and licensed
under the [MIT License](LICENSE).

This bundle provides *object* resource location services based on Puli.

Examples:

- **Static document path mapping**: Map the path `/routes` to `/cms/routes`
- **Dynamic document resolution**: Map the path `/routes` to `/cms/<current site>/routes`.
- **Access documents at a static location**: Map `/role/menu/main` to `/cms/menus/main-menu`.

The first example could use the existing CMF base route configuration to
resolve paths.

The benefit of the above two examples would be most strongly felt in
association with another component which would provide a context for the
document resource resolution.

For example, a `Site` which is matched against the incoming hostname would
provide the context with which to resolve the documents.


## Requirements

* PHP 7.1 / 7.2
* Symfony 2.8 / 3.3 / 3.4 / 4.0
* See also the `require` section of [composer.json](composer.json)

## Documentation

For the install guide and reference, see:

* [symfony-cmf/resource-bundle Documentation](http://symfony.com/doc/master/cmf/bundles/resource-rest/index.html)

See also:

* [All Symfony CMF documentation](http://symfony.com/doc/master/cmf/index.html) - complete Symfony CMF reference
* [Symfony CMF Website](http://cmf.symfony.com/) - introduction, live demo, support and community links

## Support

For general support and questions, please use [StackOverflow](http://stackoverflow.com/questions/tagged/symfony-cmf).

## Contributing

Pull requests are welcome. Please see our
[CONTRIBUTING](https://github.com/symfony-cmf/blob/master/CONTRIBUTING.md)
guide.

Unit and/or functional tests exist for this package. See the
[Testing documentation](http://symfony.com/doc/master/cmf/components/testing.html)
for a guide to running the tests.

Thanks to
[everyone who has contributed](contributors) already.

## License

This package is available under the [MIT license](src/Resources/meta/LICENSE).

[travis_legacy_badge]: https://travis-ci.org/symfony-cmf/resource-bundle.svg?branch=master
[travis_stable_badge]: https://travis-ci.org/symfony-cmf/resource-bundle.svg?branch=master
[travis_unstable_badge]: https://travis-ci.org/symfony-cmf/resource-bundle.svg?branch=master
[travis_link]: https://travis-ci.org/symfony-cmf/resource-bundle

[coveralls_legacy_badge]: https://coveralls.io/repos/github/symfony-cmf/resource-bundle/badge.svg?branch=master
[coveralls_legacy_link]: https://coveralls.io/github/symfony-cmf/resource-bundle?branch=master
[coveralls_stable_badge]: https://coveralls.io/repos/github/symfony-cmf/resource-bundle/badge.svg?branch=master
[coveralls_stable_link]: https://coveralls.io/github/symfony-cmf/resource-bundle?branch=master
[coveralls_unstable_badge]: https://coveralls.io/repos/github/symfony-cmf/resource-bundle/badge.svg?branch=master
[coveralls_unstable_link]: https://coveralls.io/github/symfony-cmf/resource-bundle?branch=master
