# Views
All the frontend html should be stored in the .phtml view files. These should be
stored in the `src/Views` folder.

Views are rendered using the view class, as shown in the example below.

```php
View::get('products/view', ['product' => $product]);
```

This example will render the file `src/Views/products/view.phtml`. The `$product`
variable is passed to the view, and can be used in the as shown below.

```php
<h1><?= $product-getName() ?></h1>
```