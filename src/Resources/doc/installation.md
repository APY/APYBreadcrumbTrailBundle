Installation
============

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
