# Annotation configuration

Add breadcumbs to the trail with annotations in your controller.

You can add annotations on the controller and the action.

## Basic example

```php
...
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

Will render the following breadcrumb trail :

> Level 1 > Level 2 > Level 3 > Level 4

## Reference

The following parameters are available :

* [label](#label) (required)
* [route](#route)
* [position](#position)
* [attributes](#attributes)

### Label

#### Basic example

See [here](#basic-example).

#### Title using @ParamConverter

The [@ParamConverter](symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#annotation-configuration) of the SensioFrameworkExtraBundle convert request parameters like 'id' to objects then injected as controller method arguments:

It is possible to display values ​​of these objects in the breadcrumb.

```php
/**
 * @Route("/book/{id}")
 * @Breadcrumb("Books")
 * @Breadcrumb("{book}")
 */
public function myAction(Book $book)
{
    /* Awesome code here */
}
```

Will render the following breadcrumb trail :

> Books > result of __toString method of $book's Object

```php
/**
 * @Route("/book/{id}")
 * @Breadcrumb("Books")
 * @Breadcrumb("{book.title}")
 */
public function myAction(Book $book)
{
    /* Awesome code here */
}
```

Will render the following breadcrumb trail :

> Books > result of getTitle method of $book's Object

**Note:** The bundle tries to call the methods : getTitle, hasTitle or isTitle.

```php
/**
 * @Route("/book/{id}")
 * @Breadcrumb("Books")
 * @Breadcrumb("{book.title:argument1}")
 */
public function myAction(Book $book)
{
    /* Awesome code here */
}
```

Will render the following breadcrumb trail :

> Books > result of getTitle('argument1') method of $book's Object

```php
/**
 * @Route("/book/{id}")
 * @Breadcrumb("Books")
 * @Breadcrumb("{book.title:argument1,argument2}")
 */
public function myAction(Book $book)
{
    /* Awesome code here */
}
```

Will render the following breadcrumb trail :

> Books > result of getTitle('argument1', ' argument2') method of $book's Object

### Route

#### Basic example

```php
/**
 * @Breadcrumb("Level 1", route={"name"="my_route"}
 * @Breadcrumb("Level 2")
 */
```

Will render the following breadcrumb trail :

> [Level 1](http://example.com) > Level 2

#### Routes with parameters

Assume that you have defined the following route :

```php
/**
 * @Route("/var/{var}", name="my_route")
 */
```

and that you are currently on the `my_action_route` with url `http://example.com/var/foo/var1/bar`

```php
/**
 * @Route("/var/{var}/var1/{var1}", name="my_action_route")
 * @Breadcrumb("Level 1", route={"name"="my_route", "parameters"={"var"=1}})
 * @Breadcrumb("Level 2", route={"name"="my_route", "parameters"={"var"="foo"}})
 * @Breadcrumb("Level 3", route={"name"="my_route", "parameters"={"var"}})
 * @Breadcrumb("Level 4", route={"name"="my_route", "parameters"={"var"="{var1}"}})
 * @Breadcrumb("Level 5")
 */
```

Will render the following breadcrumb trail :

> [Level 1](http://example.com/var/1) > [Level 2](http://example.com/var/foo) > [Level 3](http://example.com/var/foo) > [Level 4](http://example.com/var/bar) > Level 5

#### Alternative syntax

The two following expressions are equivalents :

```php
/**
 * @Breadcrumb("Level 1", route={"name"="my_route", "parameters"={"var1"=1,"var2"=2}, "absolute"=true})
 */
```

```php
/**
 * @Breadcrumb("Level 1", routeName="my_route", routeParameters={"var1"=1,"var2"=2}, routeAbsolute=true)
 */
```

### Complex parameters

Assume your controllers are designed like a REST API and you have a ManyToOne relationship on Book -> Author :

```php
/**
 * @Route("/books/{book}", name="book", requirements={"book" = "\d+"}) // example: /book/53
 * @Breadcrumb("{book.author.name}", route={"name"="author", "parameters"={"author"="{book.author.id}"}}) // example: /author/15
 * @Breadcrumb("{book.title}", route={"name"="book", "parameters"={"book"="{book.id}"}})
 * 
 * @param Request $request
 * @param Book $book
 * @return array
 */
public function indexAction(Request $request, Book $book) {
    return [
        'id'     => $book->getId(),
        'title'  => $book->getTitle(),
        'author' => $book->getAuthor()->getName(),
    ];
}
```



### Position

```php
/**
 * @Breadcrumb("Level 1")
 * @Breadcrumb("Level 2", position=1)
 * @Breadcrumb("Level 3")
 * @Breadcrumb("Level 4", position=-1)
 */
```

Will render the following breadcrumb trail :

> Level 2 > Level 1 > Level 4 > Level 3

**Note:** `position=0` will put the breacrumb to the end of the trail.

### Attributes

```php
/**
 * @Breadcrumb("Level 1", attributes={"class": "yellow", "title": "Hello world !"})
 * @Breadcrumb("Level 2")
 */
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

```php
/**
 * @Breadcrumb("Level 1")
 * @Breadcrumb()
 * @Breadcrumb("Level 2")
 * @Breadcrumb("Level 3")
 */
```

Will render the following breacrumb trail :

> Level 2 > Level 3