# PHP configuration

Add breadcumbs to the trail with PHP in your controller.


## Basic example

```php
/**
 * @Breadcrumb("Level 1")
 * @Breadcrumb("Level 2")
 */
class MyController extends Controller
{
    /**
     * @Breadcrumb("Level 3")
     */
    public function myAction()
    {
        $this->get("apy_breadcrumb_trail")->add('Level 4');
    }
}
```

Will render the following breadcrumb trail :

> Level 1 > Level 2 > Level 3 > Level 4

## Reference

```php
$this->get("apy_breadcrumb_trail")->add(
    $breadcrumb_or_title,
    $routeName,
    $routeParameters,
    $routeAbsolute,
    $position,
    $attributes
);
```

| Parameter           | Type                 | Required |
|---------------------|----------------------|----------|
| breadcrumb_or_title | Breadcrumb or string | true     |
| routeName           | string               |          |
| routeParameters     | array                |          |
| routeAbsolute       | bool                 |          |
| position            | int                  |          |
| attributes          | array                |          |


## Route

Assume that you have defined the following route :

```php
/**
 * @Route("/var/{var}", name="my_route")
 */
```

```php
/**
 * @Breadcrumb("Level 1")
 */
public function myAction()
{
    $this->get("apy_breadcrumb_trail")->add('Level 2', 'my_route', array("var" => "foo"));
    $this->get("apy_breadcrumb_trail")->add('Level 3');
}
```

Will render the following breadcrumb trail :

> Level 1 > [Level 2](http://example.com/var/foo) > Level 3

## Position

```php
/**
 * @Breadcrumb("Level 1")
 */
public function myAction()
{
    $this->get("apy_breadcrumb_trail")->add('Level 2', null, array(), false, 1);
    $this->get("apy_breadcrumb_trail")->add('Level 3');
    $this->get("apy_breadcrumb_trail")->add('Level 4', null, array(), false, -1);
}
```

Will render the following breadcrumb trail :

> Level 2 > Level 1 > Level 4 > Level 3

**Note:** `position=0` will put the breacrumb to the end of the trail.

### Reset the trail

```php
/**
 * @Breadcrumb("Level 1")
 */
public function myAction()
{
    $this->get("apy_breadcrumb_trail")
        ->reset()
        ->add('Level 2')
        ->add('Level 3');
}
```

Will render the following breadcrumb trail :

> Level 2 > Level 3