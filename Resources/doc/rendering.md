# Render a breadcrumb trail in a template


```php
...
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Breadcrumb("Level 1", route="level_1")
 * @Breadcrumb("Level 2")
 */
class MyController extends Controller
{
    /**
     * @Breadcrumb("Level 3", route="level_3")
     * @Breadcrumb("Level 4", route="level_4")
     */
    public function myAction()
    {
        /* Awesome code here */
    }
}
```

```twig
{{ apy_breadcrumb_trail_render() }}
```

The previous action `my` will render the following breadcrumb trail:

```html
<ul id="breadcrumbtrail">
    <li class="home">
        <a href="/level_1">Level 1</a>
    </li>
    <li>
        <span>Level 2</span>
    </li>
    <li>
        <a href="/level_3">Level 3</a>
    </li>
    <li class="current">
        <span>Level 4</span>
    </li>
</ul>
```

**Notes:**

* No link is displayed if no route is defined for the breadcrumb or if it's the last breadcrumb of the trail.
* The first breadcrumb will have the `home` class and the last breadcrumb will have the `current` class.
* In production environment: To update a breadcrumb translation used with annotation, you also have to update the class file where the breadcrumb is used. Then you can clear the cache.