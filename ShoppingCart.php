<?php 

class ShoppingCart {

    private $session;

    public function __construct(&$session) 
    {
        $this->session = &$session;

        if (!isset($this->session['cart_data']['total_amount'])) {
            $this->session['cart_data']['total_amount'] = 0;
        }

        if (!isset($this->session['cart_data']['total_price'])) {
            $this->session['cart_data']['total_price'] = 0.00;
        }
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

            $this->updateCart();
            
        }

    }


    public function updateCart() 
    {
        $total_price = 0;
        $total_amount = 0;

        foreach ($this->session['cart'] as $index => $value) {
            $total_price += $this->session['cart'][$index]['total_price'];
            $total_amount += $this->session['cart'][$index]['amount'];
        }

        $this->session['cart_data']['total_price'] = (float) sprintf('%.2f', $total_price);

        $this->session['cart_data']['total_amount'] = (int) $total_amount;

    }


    public function updateQuantities($post) 
    {
        foreach ($post['amounts'] as $product => $amount) {
            $amount = (int) $amount;
            if ($amount<0) { $amount = 0; }

            $this->session['cart'][$product]['amount'] = $amount;

            $this->session['cart'][$product]['total_price'] = $amount * $this->session['cart'][$product]['price'];
        }

        $this->updateCart();
    }


    public function removeFromCart($id) 
    {
        $this->session['cart'][$id] = null;
        unset($this->session['cart'][$id]);

        $this->updateCart();
    }

    public function deleteCart($id) 
    {
        $this->session['cart'] = null;
        $this->session['cart_data'] = null;
        unset($this->session['cart']);
        unset($this->session['cart_data']);
    }


}