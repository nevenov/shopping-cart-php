<?php 

class ShoppingCart {

    private $session;

    public function __construct(&$session) 
    {
        $this->session = &$session;
    }

    public function addToCart($id) 
    {
        $products = new Products();

        if(!isset($this->session['cart'][$id])) {

            $product = $products->getProductById($id);
            $this->session['cart'][$id]['name'] = $product['name'];
            $this->session['cart'][$id]['subname'] = $product['subname'];
            $this->session['cart'][$id]['url72'] = $product['url72'];
            $this->session['cart'][$id]['price'] = (float) $product['price'];

            $this->session['cart'][$id]['amount'] = 1;
            
            $this->session['cart'][$id]['total_price'] = $this->session['cart'][$id]['price'];
            
        }

    }


}