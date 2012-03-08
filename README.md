AvroGdataBundle
====================
Symfony2 bundle for Zend V1.11.11 Gdata

Usage
=====
Create a new event

``` php
$this->container->get('avro_gdata.calendar_service')->addEvent($user, $options);
```

Edit an event

``` php
$this->container->get('avro_gdata.calendar_service')->editEvent($user, $options);
```

Delete an event

``` php
$this->container->get('avro_gdata.calendar_service')->deleteEvent($user, $options);
```

User object

``` php
class User 
{
    protected $gmailUser;

    protected $gmailPassword;

    //...getters & setters
}
```

Event options array

``` php
$options = array(
    'id', 
    'startDate',
    'endDate',
    'startTime',
    'endTime',
    'tzOffset',
    'content',
    'title'
)
```

Requirements
============
- <a href="http://framework.zend.com/">Zend Framework</a>

- <a href="https://github.com/pierrre/PierrreEncrypterBundle">Pierre EncrypterBundle</a> for 2 way encrypting of users gmail password

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
    git=git://github.com:jdewit/AvroGdataBundle.git
    target=bundles/Avro/GdataBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

