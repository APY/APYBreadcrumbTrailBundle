Getting Started With BreadcrumbTrailBundle
==========================================

This bundle provides a breacrumb trail service also known as breadcrumbs or Fil d'Ariane.

Breadcrumbs can be defined with annotations or/and PHP.

**Compatibility**: The bundle is compatible with Symfony 2.0 upwards.

## Installation

Please follow the steps given [here](https://github.com/Abhoryo/APYBreadcrumbTrailBundle/blob/master/Resources/doc/installation.md) to install this bundle.

## Summary

 - [Annotation configuration](#annotation-configuration)
 - [PHP configuration](#php-configuration)
 - [Render the breadcrumb trail](#render-a-breadcrumb-trail-in-a-template)
 - [Override the template](#override-the-template)

## Usage

### Annotation configuration

Add breadcumbs to the trail with annotations in your controller.

You can add annotations on the controller and the action.

```
...
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Breadcrumb("Level 1", route="level_1")
 * @Breadcrumb("Level 2", route="level_2")
 */
class MyController extends Controller
{
    /**
     * @Breadcrumb("Level 3a", route="level_3a")
     * @Breadcrumb("Level 4a", route="level_4a")
     */
    public function aAction()
    {
        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 2 > Level 3a > Level 4a

        */
    }

    /**
     * With route parameters
     * @Breadcrumb("Level 3b")
     * @Breadcrumb("Level 4b", route={"name"="level_4b", "parameters"={"var1"=1,"var2"=2}})
     */
    public function bAction()
    {
        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 2 > Level 3b > Level 4b

        */
    }

    /**
     * With route parameters dynamically fetched in the Request
     * @Breadcrumb("Level 3c")
     * @Breadcrumb("Level 4c", route={"name"="level_4c", "parameters"={"var1","var2"=2}})
     */
    public function cRequestAction()
    {
        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 2 > Level 3c > Level 4c
        var1 will have the value given by $request->get("var1")
        */
    }

    /**
     * With an associative array of route parameters dynamically fetched in the Request
     * @Breadcrumb("Level 3d")
     * @Breadcrumb("Level 4d", route={"name"="level_4d", "parameters"={"var1"="{var2}"}})
     */
    public function dRequestAction()
    {
        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 2 > Level 3d > Level 4d
        var1 will have the value given by $request->get("var2")
        */
    }

    /**
     * With position (position=0 will put the breacrumb to the end of the trail)
     * @Breadcrumb("Level 3e", route="level_3e")
     * @Breadcrumb("Level 4e", position=2)
     */
    public function eAction()
    {
        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 4e > Level 2 > Level 3e

        */
    }

    /**
     * With negative position
     * @Breadcrumb("Level 3f", route="level_3f")
     * @Breadcrumb("Level 4f", position=-1)
     */
    public function fAction()
    {
        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 2 > Level 4f > Level 3f

        */
    }

    /**
     * Reset the trail
     * @Breadcrumb("Level 3f", route="level_3f")
     * @Breadcrumb()
     * @Breadcrumb("Level 1g", route="level_1g")
     * @Breadcrumb("Level 2g", route="level_2g")
     */
    public function gAction()
    {
        /*

        This action will show the following breacrumb trail:
        Level 1g > Level 2g

        */
    }

    /**
     * Add extra attributes for the breadcrumb
     * @Breadcrumb("Level 2h", route="level_2h", attributes={"class" : "yellow", "title" : "Hello world !"})
     */
    public function hAction()
    {
        /*

        This action will show the following breacrumb trail:
        Level 1h > Level 2h

        Level 2h will have additional attributes in the template

        */
    }

}
```

#### Title with @ParamConverter

The [@ParamConverter](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#annotation-configuration) of the SensioFrameworkExtraBundle convert request parameters like 'id' to objects then injected as controller method arguments:

It is possible to display values ​​of these objects in the breadcrumb.


```
...
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/book/{id}")
 * @Breadcrumb("Books")
 * @Breadcrumb("{book}")
 */
public function aShowAction(Book $book)
{

    /*

    This action will show the following breacrumb trail:
    Books > result of __toString method of $book's Object

    */

}

/**
 * @Route("/book/{id}")
 * @Breadcrumb("Books")
 * @Breadcrumb("{book.title}")
 */
public function bShowAction(Book $book)
{

    /*

    This action will show the following breacrumb trail:
    Books > result of getTitle method of $book's Object

    The bundle tries to call the methods : getTitle, hasTitle or isTitle

    */

}

/**
 * @Route("/book/{id}")
 * @Breadcrumb("Books")
 * @Breadcrumb("{book.title:argument1}")
 */
public function cShowAction(Book $book)
{

    /*

    This action will show the following breacrumb trail:
    Books > result of getTitle('argument1') method of $book's Object

    */

}

/**
 * @Route("/book/{id}")
 * @Breadcrumb("Books")
 * @Breadcrumb("{book.title:argument1,argument2}")
 */
public function dShowAction(Book $book)
{

    /*

    This action will show the following breacrumb trail:
    Books > result of getTitle('argument1', ' argument2') method of $book's Object

    */

}

```

---

### PHP configuration

Add breadcumbs to the trail with PHP in your controller.

```
...
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Breadcrumb("Level 1", route="level_1")
 * @Breadcrumb("Level 2")
 */
class MyController extends Controller
{
    /**
     * @Breadcrumb("Level 3a", route="level_3a")
     */
    public function aAction()
    {
        $this->get("apy_breadcrumb_trail")->add('Level 4a', 'level_4a');

        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 2 > Level 3a > Level 4a

        */
    }

    /**
     * With route parameters
     * @Breadcrumb("Level 3b")
     */
    public function bAction()
    {
        $this->get("apy_breadcrumb_trail")->add('Level 4b', 'level_4b', array("var1" => 1,"var2" => 2));

        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 2 > Level 3b > Level 4b

        */
    }

    /**
     * With position (position=0 will put the breacrumb to the end of the trail)
     * @Breadcrumb("Level 3c", route="level_3c")
     */
    public function cAction()
    {
        $this->get("apy_breadcrumb_trail")->add('Level 4c', 'level_4c', array(), false, 2);
        // The fourth argument is the absolute option of a route

        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 4c > Level 2 > Level 3c

        */
    }

    /**
     * With negative position
     * @Breadcrumb("Level 3d", route="level_3d")
     */
    public function dAction()
    {
        $this->get("apy_breadcrumb_trail")->add('Level 4d', 'level_4d', array(), false, -1);
        // The fourth argument is the absolute option of a route

        /*

        This action will show the following breacrumb trail:
        Level 1 > Level 2 > Level 4d > Level 3d

        */
    }

    /**
     * Reset the trail
     * @Breadcrumb("Level 3d", route="level_3d")
     */
    public function eAction()
    {
        $this->get("apy_breadcrumb_trail")
            ->reset()
            ->add('Level 1e', 'level_1e')
            ->add('Level 2e', 'level_2e')

        /*

        This action will show the following breacrumb trail:
        Level 1e > Level 2e

        */
    }
}
```

---

### Render a breadcrumb trail in a template

`{{ apy_breadcrumb_trail_render() }}`

The action `a` will render the following breadcrumb trail:

```
<ul id="breadcrumbtrail">
    <li class="home">Home</li>
    <li><a href="/level_1">Level 1</a></li>
    <li>Level 2b</li>
    <li><a href="/level_3a">Level 3a</a></li>
    <li class="current"><a href="/level_4a">Level 4a</a></li>
</ul>
```

**Notes:**

- No link is displayed if no route is defined for the breadcrumb or if it's the last breadcrumb of the trail.
- The first breadcrumb will have the `home` class and the last breadcrumb will have the `current` class.
- In production environment: To update a breadcrumb translation used with annotation, you also have to update the class file where the breadcrumb is used. Then you can clear the cache.

---

### Override the template
You can override the default template in many ways.

 - You can put your new template in the app folder:

`app/Resources/APYBreadcrumbTrailBundle/views/breadcrumbtrail.html.twig`

 - You can define the template in your config.yml file:

```
apy_breadcrumb_trail:
    template: APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig
```

 - You can define the template in a breadcrumb annotation:

```
@Breadcrumb("My breadcrumb", route="my_route")
@Breadcrumb(template="APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig")

OR

@Breadcrumb("My breadcrumb", route="my_route", template="APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig")
```

 - You can define the template in PHP:

```
$this->get("apy_breadcrumb_trail")->setTemplate('APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig');
```

 - You can define the template when you render the breadcrumb trail in your twig file:

`{{ apy_breadcrumb_trail_render('APYBreadcrumbTrailBundle::breadcrumbtrail.html.twig') }}`

---

These expressions generate the same breadcrumb.

    @Breadcrumb("Level 4b", route={"name"="level_4b", "parameters"={"var1"=1,"var2"=2}, "absolute"=true})
    @Breadcrumb("Level 4b", routeName="level_4b", routeParameters={"var1"=1,"var2"=2}, routeAbsolute=true)


And these too.

    @Breadcrumb("Level 4b", route="level_4b")
    @Breadcrumb("Level 4b", route={"name"="level_4b"})
    @Breadcrumb("Level 4b", routeName="level_4b")

### Todo

 * Issue #2
