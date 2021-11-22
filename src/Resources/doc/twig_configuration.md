# Twig configuration

Adding breadcumbs to the trail in your template directly works exactly like the [PHP configuration](php_configuration.md).
In order to do so, you just have to add the service in a global variable.

## Configuration

```yml
#app/config/config.yml
twig:
    globals:
        breadcrumb_trail: "@APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail"
```

## Basic example

```twig
<!-- MyProject\MyBundle\Resources\views\myTemplate.html.twig -->
{% do breadcrumb_trail.add('My new breadcrumb') %}
{{ apy_breadcrumb_trail_render() }}
```

Functions are chainable so you can write this code:

```twig
<!-- MyProject\MyBundle\Resources\views\myTemplate.html.twig -->
{% do breadcrumb_trail.reset().add('breadcrumb 1').add('breadcrumb 2') %}
{{ apy_breadcrumb_trail_render() }}
```
