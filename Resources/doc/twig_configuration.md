# Twig configuration

Add breadcumbs to the trail with Twig in your template works exactly like the [PHP configuration](php_configuration.md).  
You just have to add the service in a global variable.

## Configuration

```yml
#app/config/config.yml
twig:
    globals:
        breadcrumb_trail: "@apy_breadcrumb_trail"
```

## Basic example

```django
<!-- MyProject\MyBundle\Resources\views\myTemplate.html.twig -->
{% do breadcrumb_trail.add('My new breadcrumb') %}
{{ apy_breadcrumb_trail_render() }}
```

Functions are chainable so you can write this code:

```django
<!-- MyProject\MyBundle\Resources\views\myTemplate.html.twig -->
{% do breadcrumb_trail.reset().add('breadcrumb 1').add('breadcrumb 2') %}
{{ apy_breadcrumb_trail_render() }}
```
