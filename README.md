# yii2-facebook

Helper class to work with Facebook services and API.

Installation
------------

Install package by composer
```composer
{
    "require": {
       "strong2much/yii2-facebook": "dev-master"
    }
}

Or

$ composer require strong2much/yii2-facebook "dev-master"
```

Use the following code in your configuration file to work with facebook graph api
```php
'facebook' => [
    'class' => 'strong2much\facebook\Api'
]
```

Use the following code to run widget in view:
```php
echo strong2much\facebook\widgets\LikeBoxWidget::widget([
    'appId' => '',
    'url' => '',
]);
```