AvroGdataBundle
====================

Installation
============

Add the `Avro` namespace to your autoloader:

``` php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Avro' => __DIR__.'/../vendor/bundles',
));
```

Enable the bundle in the kernel:

``` php
// app/AppKernel.php

    new Avro\GdataBundle\AvroGdataBundle
```

```
[AvroGdataBundle]
    git=git://github.com/yourGitHubAccount.git
    target=bundles/Avro/GdataBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

