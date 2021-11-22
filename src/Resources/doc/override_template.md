# Override the template
You can override the default template in several ways:

 - You can put your new template in the app folder:

`templates/bundles/APYBreadcrumbTrailBundle/breadcrumbtrail.html.twig`

 - You can define the template in your config.yml file:

```yaml
apy_breadcrumb_trail:
    template: "@APYBreadcrumbTrail/breadcrumbtrail.html.twig"
```

 - You can define another template in a breadcrumb annotation:

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Breadcrumb("My breadcrumb", route: "my_route")]
#[Breadcrumb(template: "@APYBreadcrumbTrail/breadcrumbtrail.html.twig")]
```

Or

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Breadcrumb("My breadcrumb", route: "my_route", template: "@APYBreadcrumbTrail/breadcrumbtrail.html.twig")]
```

 - You can define the template in PHP:

```php
/**
 * @see \APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail::setTemplate()
 */
$trail->setTemplate('@APYBreadcrumbTrail/breadcrumbtrail.html.twig');
```

 - You can define the template when you render the breadcrumb trail in your twig file:

```twig
{{ apy_breadcrumb_trail_render('@APYBreadcrumbTrail/breadcrumbtrail.html.twig') }}
```
