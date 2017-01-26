# Symfony CMF Resource Bundle

[![Build Status](https://travis-ci.org/symfony-cmf/resource-bundle.svg?branch=master)](https://travis-ci.org/symfony-cmf/resource-bundle)
[![StyleCI](https://styleci.io/repos/26688804/shield)](https://styleci.io/repos/26688804)
[![Latest Stable Version](https://poser.pugx.org/symfony-cmf/resource-bundle/version.png)](https://packagist.org/packages/symfony-cmf/resource-bundle)
[![Total Downloads](https://poser.pugx.org/symfony-cmf/resource-bundle/d/total.png)](https://packagist.org/packages/symfony-cmf/resource-bundle)

This bundle is part of the [Symfony Content Management Framework (CMF)](http://cmf.symfony.com/)
and licensed under the [MIT License](LICENSE).

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

* Symfony 2.8+
* See also the `require` section of [composer.json](composer.json)

## Documentation

Not yet.

* [All Symfony CMF documentation](http://symfony.com/doc/master/cmf/index.html) - complete Symfony CMF reference
* [Symfony CMF Website](http://cmf.symfony.com/) - introduction, live demo, support and community links

## Contributing

Pull requests are welcome. Please see our
[CONTRIBUTING](https://github.com/symfony-cmf/symfony-cmf/blob/master/CONTRIBUTING.md)
guide.

Unit and/or functional tests exist for this bundle. See the
[Testing documentation](http://symfony.com/doc/master/cmf/components/testing.html)
for a guide to running the tests.

Thanks to
[everyone who has contributed](https://github.com/symfony-cmf/ResourceBundle/contributors) already.
## Running the tests
