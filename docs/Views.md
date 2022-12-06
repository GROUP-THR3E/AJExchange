# Views
All the frontend html should be stored in the .phtml view files. These should be
stored in the `src/Views` folder. The paths should reflect the routes the pages are
accessed. For example, the view for `/products/view` should be stored in
`/src/Views/products/view.phtml`.

Views will use `_layout.phtml` as a template for the page, and the named
view's contents will be put in the body of `_layout.phtml`.

Views are rendered using the view class, as shown in the example below.

```php
View::get('products/view', ['product' => $product]);
```

This example will render the file `src/Views/products/view.phtml`. The `$product`
variable is passed to the view, and can be used in the as shown below.

```php
<h1><?= $product-getName() ?></h1>
```