<?php 

class Controller {

    const BASE_URL = 'http://localhost/lessons/paypal/shopping-cart/';


    public function __construct(&$session)
    {
        $this->session = &$session;
    }


    public function showCart()
    {
        $cart_products = $this->session['cart'] ?? [];
        $total_price = $this->session['cart_data']['total_price'] ?? 0;
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
        $cart = new ShoppingCart($this->session);
        $cart->addToCart($id);

        return header("Location: " . self::BASE_URL . "?action=cart");
    }


    public function removeFromCart($id) 
    {
        $cart = new ShoppingCart($this->session);
        $cart->removeFromCart($id);

        return header("Location: " . self::BASE_URL . "?action=cart");
    }


    public function checkout()
    {
        $cart_products = $this->session['cart'] ?? [];
        $total_price = $this->session['cart_data']['total_price'] ?? 0;
        $total_amount = $this->session['cart_data']['total_amount'] ?? 0;
        require_once('layout/views/checkout.php');
    }


    public function updateQuantities($post) 
    {
        $cart = new ShoppingCart($this->session);
        $cart->updateQuantities($post);       

        return header("Location: " . self::BASE_URL . "?action=cart");
    }

    
    public function login() 
    {
        $login = new FakeLogin($this->session);
        $login->login();
        return header("Location: " . self::BASE_URL);
    }


    public function logout() 
    {
        $login = new FakeLogin($this->session);
        $login->logout();
        return header("Location: " . self::BASE_URL);
    }


    public function createPayment() 
    {
        if(!isset($this->session['logged_in'])) {
            return header("Location: " . self::BASE_URL);
        }
    }



}

