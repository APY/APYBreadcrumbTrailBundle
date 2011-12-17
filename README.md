Getting Started With BreadcrumbTrailBundle
==========================================

This bundle provides a breacrumb trail service also known as breadcrumbs or Fil d'Ariane.

Breadcrumbs can be define with annotations or PHP.

**Compatibility**: Symfony 2.0+

## Installation

Please follow the steps given [here](https://github.com/Abhoryo/APYBreadcrumbTrailBundle/blob/master/Resources/doc/installation.md) to install this bundle.

## Usage

### Render a breadcrumb trail in a template

    {{ apy_breadcrumb_trail_render() }}


### Add breadcumbs to the trail with annotations or PHP in your controller.

    ...
    use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

    /**
     * @Breadcrumb("Home")
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
            ...
        }

        /**
         * @Breadcrumb("Level 3b")
         * @Breadcrumb("Level 4b", route={"name"="level_4b", "parameters"={"var1"=1,"var2"=2}})
         */
        public function bAction()
        {
            $this->get("apy_breadcrumb_trail")
                ->add('Level 5b', 'level_5b', array('var3'=>3))
                ->add('Level 6b', 'level_6b');
            ...
        }
    }

Will turn into:

    <ul id="breadcrumbtrail">
        <li class="home">Home</li>
        <li><a href="/level_1">Level 1</a></li>
        <li><a href="/level_2">Level 2</a></li>
        <li>Level 3b</li>
        <li><a href="/level_4b/1/2">Level 4b</a></li>
        <li><a href="/level_5b/3">Level 5b</a></li>
        <li class="current">Level 6b</li>
    </ul>

**Notes:**

- No link is displayed if no route is defined for the breadcrumb or if it's the last breadcrumb of the trail.
- The first breadcrumb will have the `home` class and the last breadcrumb will have the `current` class.

---

These expressions generate the same breadcrumb.

    @Breadcrumb("Level 4b", route={"name"="level_4b", "parameters"={"var1"=1,"var2"=2}, "absolute"=true})
    @Breadcrumb("Level 4b", routeName="level_4b", routeParameters={"var1"=1,"var2"=2}, routeAbsolute"=true)


And these too.

    @Breadcrumb("Level 4b", route="level_4b")
    @Breadcrumb("Level 4b", route={"name"="level_4b"})
    @Breadcrumb("Level 4b", routeName="level_4b")
