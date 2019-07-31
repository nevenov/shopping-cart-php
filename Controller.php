<?php 

class Controller {

    const BASE_URL = 'http://localhost/lessons/paypal/shopping-cart/';

    public function showCart()
    {
        require_once('layout/views/cart.php');
    }


    public function showProducts()
    {
        $products = new Products();

        $products = $products->getAllProducts();

        require_once('layout/views/products.php');
    }


    public function addToCart($id) 
    {   
        // to do: add to cart
        return header("Location: " . self::BASE_URL . "?action=cart");
    }


    public function removeFromCart($id) 
    {
        // to do: remove from cart
        return header("Location: " . self::BASE_URL . "?action=cart");
    }


    public function checkout()
    {
        require_once('layout/views/checkout.php');
    }


    public function updateQuantities($post) 
    {
        // to do: update quantities
        return header("Location: " . self::BASE_URL . "?action=cart");
    }

    public function login() 
    {
        // to do: login
        return header("Location: " . self::BASE_URL);
    }

    public function logout() 
    {
        // to do: logout
        return header("Location: " . self::BASE_URL);
    }

}
