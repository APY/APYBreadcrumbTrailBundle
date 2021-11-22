Installation
============

The most recent versions of Composer add the bundle to the configuration for you, but
in case an earlier version of Symfony is used the bundle can get added to the list
of bundles in your AppKernel class as shown below:

```
composer require apy/breadcrumbtrail-bundle
```

## Enable the bundle in kernel

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle(),
    ];
}
```
