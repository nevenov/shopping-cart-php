<?php 

class Router {

    public function __construct($get, $post, &$session)
    {

        $controller = new Controller($session);

        if (isset($get['action'])) {
            
            switch($get['action']) {

                case 'cart':
                    $controller->showCart();
                    break;

                case 'add-to-cart':
                    $controller->addToCart($get['id']);
                    break;
                
                case 'remove-from-cart':
                    $controller->removeFromCart($get['id']);
                    break;

                case 'checkout':
                    $controller->checkout();
                    break;

                case 'update-quantities':
                    $controller->updateQuantities($post);
                    break;

                case 'create-payment':
                    $controller->createPayment();
                    break;

                case 'login':
                    $controller->login();
                    break;
                    
                case 'logout':
                    $controller->logout();
                    break;

                default:
                $controller->showProducts();
            }

        } else {
            $controller->showProducts();
        }


    }


}