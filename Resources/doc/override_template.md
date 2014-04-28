# Override the template
You can override the default template in many ways.

 - You can put your new template in the app folder:

`app/Resources/APYBreadcrumbTrailBundle/views/breadcrumbtrail.html.twig`

 - You can define the template in your config.yml file:

```yaml
apy_breadcrumb_trail:
    template: APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig
```

 - You can define the template in a breadcrumb annotation:

```php
/**
 * @Breadcrumb("My breadcrumb", route="my_route")
 * @Breadcrumb(template="APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig")
 */
```

Or

```php
/**
 * @Breadcrumb("My breadcrumb", route="my_route", template="APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig")
 */
```

 - You can define the template in PHP:

```php
$this->get("apy_breadcrumb_trail")->setTemplate('APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig');
```

 - You can define the template when you render the breadcrumb trail in your twig file:

```twig
{{ apy_breadcrumb_trail_render('APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig') }}
```