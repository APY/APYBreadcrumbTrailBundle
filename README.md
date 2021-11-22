Getting Started With BreadcrumbTrailBundle
==========================================

This bundle provides a breadcrumb trail service also known as breadcrumbs or Fil d'Ariane.
Breadcrumbs can be defined with Attributes, annotations, PHP and Twig.

## Installation

Please follow the steps given in [installation.md](src/Resources/doc/installation.md) to install this bundle.

## Bundle documentation

- [Annotation configuration](src/Resources/doc/annotation_configuration.md)
- [PHP configuration](src/Resources/doc/php_configuration.md)
- [Twig configuration](src/Resources/doc/twig_configuration.md)
- [Render the breadcrumb trail](src/Resources/doc/rendering.md)
- [Override the template](src/Resources/doc/override_template.md)

## Tests

Several make targets can get used to run the PHPUnit test suite on different PHP environments:

```
$ make test
$ make test-php73
$ make test-php74-lowest
```

In case all test suites pass but running tests still returns an error code, that
might be related to the number of allowed deprecations. Make sure that the
`SYMFONY_DEPRECATIONS_HELPER` value of `max[self]` as found in `phpunit.xml.dist`
matches the "Remaining self deprecation notices" count from the test runner output.

## Code style

PHP-CS-Fixer is used to keep the code style in shape. There is a make target that uses Docker to fix
the code style without having to install any other dependencies:

```
$ make cs
```

## Static code analysis

PHPStan is used to keep the code quality up to par. There is a make target that uses Docker to test
the code quality without having to install any other dependencies:

```
$ make static
```
