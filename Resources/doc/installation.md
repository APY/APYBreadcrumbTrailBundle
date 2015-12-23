Installation
============

## Step 1: Download BreadcrumbTrailBundle

Ultimately, the BreadcrumbTrailBundle files should be downloaded to the
`vendor/bundles/APY/BreadcrumbTrailBundle` directory.

This can be done in several ways, depending on your preference. The first
method is the standard Symfony2 method.

**Using the vendors script**

Add the following lines in your `deps` file:

```
[BreadcrumbTrailBundle]
    git=git://github.com/Abhoryo/APYBreadcrumbTrailBundle.git
    target=bundles/APY/BreadcrumbTrailBundle
```

Now, run the vendors script to download the bundle:

```bash
$ php bin/vendors install
```

**Using submodules**

If you prefer instead to use git submodules, the run the following:

```bash
$ git submodule add git://github.com/Abhoryo/APYBreadcrumbTrailBundle.git vendor/bundles/APY/BreadcrumbTrailBundle
$ git submodule update --init
```

**Using composer**

run:

`php composer.phar require apy/breadcrumbtrail-bundle`


## Step 2: Configure the Autoloader (not with composer)

Add the `APY` namespace to your autoloader:

```php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'APY' => __DIR__.'/../vendor/bundles',
));
```

## Step 3: Enable the bundles

Finally, enable the bundles in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new APY\BreadcrumbTrailBundle\APYBreadcrumbTrailBundle(),
    );
}
```
