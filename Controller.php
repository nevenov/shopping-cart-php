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

        if (!isset($this->session['cart']) || empty($this->session['cart'])) {
            return header("Location: " . self::BASE_URL);
        }

        // we include the paypal rest-api-sdk-php library here
        require __DIR__ . "/vendor/autoload.php";

        // API call - this code will be initialized by the payer:
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');
        
        // according to http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/CreatePaymentUsingPayPal.html
        // we need to loop through all the products saved in the shopping cart session
        foreach ($this->session['cart'] as $product) {
            
            $items[] = $item = new PayPal\Api\Item();
            $item->setName($product['name'])
                ->setCurrency('USD')
                ->setQuantity($product['amount'])
                ->setPrice($product['price']);
        }
        
        // initialize the items list to the Paypal API
        $itemList = new PayPal\Api\ItemList();
        $itemList->setItems($items);

        // initialize the payment details to the Paypal API
        $details = new PayPal\Api\Details();
        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($this->session['cart_data']['total_price']);
        
        // initialize the amount to the Paypal API   
        $amount = new PayPal\Api\Amount();
        $amount->setCurrency("USD")
            ->setTotal($this->session['cart_data']['total_price'])
            ->setDetails($details);
        
        // initialize the transaction to the Paypal API 
        $transaction = new PayPal\Api\Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

        // initialize the redirect URLs to the Paypal API 
        $redirectUrls = new PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl(self::BASE_URL . "?action=execute-payment")
            ->setCancelUrl(self::BASE_URL . "?action=cart");

        $payment = new PayPal\Api\Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));


        // We need to authenticate to Paypal API:
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AUA0vvs7vganK5I5g3EAJAgtpxxlt4WzlOK9MuMHDavYnWux5XkocZ2FaOfbzNBODM5kDf2LbeknZ5It',     // ClientID
                'EM-6C_MAmtbve4UrBMNib7tRfQEl-0spuGYubHhSUQK9fLWqa6R6p1Y1ongtE7VnXj29Mu8gFLfVARD3'      // ClientSecret
            )
        );

        $payment->create($apiContext);

        $approvalUrl = $payment->getApprovalLink();

        print_r($approvalUrl);
    }



}

