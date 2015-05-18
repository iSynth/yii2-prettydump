[![Packagist Version](https://img.shields.io/packagist/v/sb/yii2-prettydumper.svg?style=flat-square)](https://packagist.org/packages/sb/yii2-prettydumper)
[![Total Downloads](https://img.shields.io/packagist/dt/sb/yii2-prettydumper.svg?style=flat-square)](https://packagist.org/packages/sb/yii2-prettydumper)


Yii2 pretty var dumper
================
Dump any PHP types and objects to browser or console.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist sb/yii2-prettydumper "*"
```

or add

```
"sb/yii2-prettydumper": "*"
```

to the require section of your `composer.json` file.

Usage example with simple controller
------------------------------------

```php
<?php

    namespace frontend\controllers;

    // add Yii components namespaces
    use Yii;
    use yii\web\Controller;

    // add prettydumper component namespace
    use sb\prettydumper;

    function dump($var, $return = false)
    {
		$output = Dumper::Dump($var);

		if($return)
		{
			return $output;
		}
		else
		{
			echo $output;
		}
	}

	function roll()
	{
		foreach (func_get_args() as $val) dump($val);
	}

    class SiteController extends Controller
    {
        public function actionTest()
        {
            // dump arrays
            dump($_SERVER);

            // dump objects
            $ob = new stdClass();
            $ob->property = 'This is a object property value';
            dump($ob);

            // dump any internal PHP types
            dump("String 1");
            dump(100);
            dump(0.10);
            dump(true);

            // roll any values
            roll($_SERVER, $ob, 'String 2', 200, 0.20, false);
        }
    }
```
