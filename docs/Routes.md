# Routes

The planned routes for the application are as follows. `{x}` indicates a url
parameter.

## Route List

- `/` - index page
- `/listings` - view all listings
- `listings/search?q={0}` - search listings
- `/listings/view?id={0}` - view listing
- `/listings/create` - create new listings
- `/listings/delete` - delete listing
- `/listings/exchange` - buy/swap/take listing
- `/users/login` - user login

## Setting Up Routes

Setting up basic routes can be found [here](https://www.slimframework.com/docs/v4/objects/routing.html).
The main sections to read here are setting up basic GET and POST routes.

Routes should be organised into controllers. These files should be put in the
`src/Controllers` folder. For example, all routes under `/products` may be put in one
controller, as shown below.

```php
return function(App $app) {
    $app->get('/products', function(Request $request, Response $response) {
        $products = db::getProducts();
        $view = View::get('products/index', ['products' => $products]);
        $response->getBody()->write($view);
        return $response;
    });
    
    //...
    
    $app->post('/products/create', function(Request $request, Response $response, array $args) {
        $success = db::createProduct($args['name'], $args['price']);
        if ($success === true) {
            return $response
                ->withResponse('Location: /products');
                ->withStatus(302);
        } else {
            $view = View::get('/products/create', ['success' => false]);
            $response->getBody()->write($view);
            return $response;
        }
    });
}
```