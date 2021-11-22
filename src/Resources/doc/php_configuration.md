# PHP configuration

How to add breadcumbs to the trail in the controller.

## Example 1) Injected via the controller's constructor

Autowiring has to be enabled for the folder where the controller is located. Symfony 4 and higher
by default have autowiring enabled.

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;

#[Breadcrumb("Level 1")]
#[Breadcrumb("Level 2")]
class MyController extends Controller
{
   private $trail;

   public function __construct(Trail $trail)
   {
        $this->trail = $trail;
    }

    #[Breadcrumb("Level 3")]
    public function myAction()
    {
        $this->trail->add('Level 4');
    }
}
```

The above example will render the following breadcrumb trail:

> Level 1 > Level 2 > Level 3 > Level 4

## Example 2) by autowiring the trail to the action callable)

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;

#[Breadcrumb("Level 1")]
#[Breadcrumb("Level 2")]
class MyController extends Controller
{
    #[Breadcrumb("Level 3")]
    public function myAction(Trail $trail)
    {
        $trail->add('Level 4');
    }
}
```

Will render the following breadcrumb trail :

> Level 1 > Level 2 > Level 3 > Level 4

## Method reference

```php
/**
 * @see APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail::add()
 */
$trail->add(
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
use Symfony\Component\Routing\Annotation\Route;

#[Route("/var/{var}", name: "my_route")]
```

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;

#[Breadcrumb("Level 1")]
public function myAction(Trail $trail)
{
    $trail->add('Level 2', 'my_route', ["var" => "foo"]);
    $trail->add('Level 3');
}
```

Will render the following breadcrumb trail :

> Level 1 > [Level 2](http://example.com/var/foo) > Level 3

## Position

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;

#[Breadcrumb("Level 1")]
public function myAction(Trail $trail)
{
    $trail->add('Level 2', null, [], false, 1);
    $trail->add('Level 3');
    $trail->add('Level 4', null, [], false, -1);
}
```

Will render the following breadcrumb trail :

> Level 2 > Level 1 > Level 4 > Level 3

**Note:** `position=0` will put the breadcrumb to the end of the trail.

### Reset the trail

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail;

#[Breadcrumb("Level 1")]
public function myAction(Trail $trail)
{
    $trail
        ->reset()
        ->add('Level 2')
        ->add('Level 3')
    ;
}
```

Will render the following breadcrumb trail :

> Level 2 > Level 3
