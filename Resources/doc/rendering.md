# Render a breadcrumb trail in a template

`{{ apy_breadcrumb_trail_render() }}`

The action `a` will render the following breadcrumb trail:

```html
<ul id="breadcrumbtrail">
    <li class="home">Home</li>
    <li><a href="/level_1">Level 1</a></li>
    <li>Level 2b</li>
    <li><a href="/level_3a">Level 3a</a></li>
    <li class="current"><a href="/level_4a">Level 4a</a></li>
</ul>
```

**Notes:**

* No link is displayed if no route is defined for the breadcrumb or if it's the last breadcrumb of the trail.
* The first breadcrumb will have the `home` class and the last breadcrumb will have the `current` class.
* In production environment: To update a breadcrumb translation used with annotation, you also have to update the class file where the breadcrumb is used. Then you can clear the cache.