# Configuration

Add breadcumbs to the trail with annotations in your controller, invokable controller
or any other callable. You can add breadcrumbs using annotations, attributes or PHP.

Since version 1.7 annotations are deprecated and using PHP attributes is preferred.
Annotations and will be removed in 2.0. After the below basic example all
documentation will use attributes.

## Basic example

### Annotations

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Breadcrumb("Level 1")
 * @Breadcrumb("Level 2")
 */
class MyController extends Controller
{
    /**
     * @Breadcrumb("Level 3")
     * @Breadcrumb("Level 4")
     */
    public function myAction()
    {
        /* Awesome code here */
    }
}
```

### PHP 8 Attributes

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Breadcrumb(title: 'Level 1')]
#[Breadcrumb(title: 'Level 2')]
class MyController extends Controller
{
    #[Route('/my', name: 'my_action')]
    #[Breadcrumb(title: 'Level 3')]
    #[Breadcrumb(title: 'Level 4')]
    public function myAction(): Response
    {
        /* Awesome code here */
    }
}
```

Will render the following breadcrumb trail:

> Level 1 > Level 2 > Level 3 > Level 4

## Reference

The following parameters are available:

* [title](#title) (required)
* [routeName](#route-name)
* [routeParameters](#route-parameters)
* [routeAbsolute](#route-absolute)
* [position](#position)
* [template](#template)
* [html attributes](#html-attributes)

A full version of the Attribute looks like:

```php
#[Breadcrumb(
    title: 'string',
    routeName: 'string',
    routeParameters: 'array',
    routeAbsolute: 'bool',
    position: 'int',
    template: 'string',
    attributes: 'array'
#)]
```

### Title

#### Basic example

See [here](#basic-example).

#### Title using @ParamConverter

The [@ParamConverter](symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#annotation-configuration) of the SensioFrameworkExtraBundle convert request parameters like 'id' to objects then injected as controller method arguments:

It is possible to display values of these objects in the breadcrumb.

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Route("/book/{id}")
#[Breadcrumb("Books")]
#[Breadcrumb("{book}")]
public function myAction(Book $book)
{
    /* Awesome code here */
}
```

Will render the following breadcrumb trail :

> Books > "result of __toString method of $book's Object"

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Route("/book/{id}")
#[Breadcrumb("Books")]
#[Breadcrumb("{book.title}")]
public function myAction(Book $book)
{
    /* Awesome code here */
}
```

Will render the following breadcrumb trail :

> Books > result of getTitle method of $book's Object

**Note:** The bundle tries to call the methods : `getTitle`, `hasTitle` or `isTitle`.

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Route("/book/{id}")
#[Breadcrumb("Books")]
#[Breadcrumb("{book.title:argument1}")]
public function myAction(Book $book)
{
    /* Awesome code here */
}
```

Will render the following breadcrumb trail :

> Books > result of getTitle('argument1') method of $book's Object

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Route("/book/{id}")
#[Breadcrumb("Books")]
#[Breadcrumb("{book.title:argument1,argument2}")]
public function myAction(Book $book)
{
    /* Awesome code here */
}
```

Will render the following breadcrumb trail :

> Books > result of getTitle('argument1', ' argument2') method of $book's Object

### Route name

Assume that you have defined the following route :

```php
use Symfony\Component\Routing\Annotation\Route;

#[Route('/var/{var}', name: 'my_route')]
```

#### Basic example

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Breadcrumb("Level 1", routeName: 'my_route')]
#[Breadcrumb("Level 2")]
```

Will render the following breadcrumb trail :

> [Level 1](http://example.com) > Level 2

### Route parameters

When your the current request context is that you are the `my_action_route` with
url `http://example.com/var/foo/var1/bar`.

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/var/{var}/var1/{var1}", name: 'my_action_route')]
#[Breadcrumb("Level 1", routeName: 'my_route', routeParameters: ['var' => '1'])]
#[Breadcrumb("Level 2", routeName: 'my_route', routeParameters: ['var' => 'foo'])]
#[Breadcrumb("Level 3", routeName: 'my_route', routeParameters: ['var'])]
#[Breadcrumb("Level 4", routeName: 'my_route', routeParameters: ['var' => '{var1}'])]
#[Breadcrumb("Level 5")]
```

Will render the following breadcrumb trail :

> [Level 1](http://example.com/var/1) > [Level 2](http://example.com/var/foo) > [Level 3](http://example.com/var/foo) > [Level 4](http://example.com/var/bar) > Level 5

### Complex parameters

Assume your controllers are designed like a REST API and you have a `ManyToOne` relationship on `Book -> Author` :

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/books/{book}", name: 'book', requirements: ['book' => "\d+"])]  // example: /book/53
#[Breadcrumb({book.author.name}, routeName: 'author', routeParameters: ['author' => {book.author.id}])]  // example: /author/15
#[Breadcrumb({book.title}, routeName: 'book', routeParameters: ['book' => {book.id}])]
public function indexAction(Request $request, Book $book): array {
    return [
        'id'     => $book->getId(),
        'title'  => $book->getTitle(),
        'author' => $book->getAuthor()->getName(),
    ];
}
```

Will render the following breadcrumb trail :

> [Author Name](/author/15) > Book title


### Route absolute

Passing `routeAbsolute` will inform the router to render the routes as absolute, based on
the current router request context.

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Breadcrumb("Level 1", routeName: 'my_route', routeParameters: ['var1' => 1, 'var2' => 2], routeAbsolute: true)]
```

### Position

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Breadcrumb("Level 1")]
#[Breadcrumb("Level 2", position: 1)]
#[Breadcrumb("Level 3")]
#[Breadcrumb("Level 4", position: -1)]
```

Will render the following breadcrumb trail :

> Level 2 > Level 1 > Level 4 > Level 3

**Note:** `position=0` will put the breacrumb to the end of the trail.

### HTML Attributes

The `attributes` parameter can be provided to show attributes in the rendered html.

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Breadcrumb("Level 1", attributes: ["class" => "yellow", "title" => "Hello world !"])]
#[Breadcrumb("Level 2")]
```

Will render the following breadcrumb trail :

```html
<ul id="breadcrumbtrail">
    <li class="home">Home</li>
    <li class="yellow" title="Hello world !">
        <span>Level 1</span>
    </li>
    <li class="current">
        <span>Level 2</span>
    </li>
</ul>
```

## Extra

### Reset the trail

Passing an breadcrumb without any parameter values will remove all existing
breadcrumbs from the trail.

Resetting might come in handy in case the controller class unwantedly defines
breadcrumbs already.

```php
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

#[Breadcrumb("Level 1")]
#[Breadcrumb()]
#[Breadcrumb("Level 2")]
#[Breadcrumb("Level 3")]
```

Will render the following breadcrumb trail :

> Level 2 > Level 3
