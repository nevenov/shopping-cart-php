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

        //this will redirect our shopping cart to Paypal payment page. Write print_r($approvalUrl) to see the URL
        return header("Location: " . $approvalUrl);
        
    }


    public function executePayment() 
    {
        if(!isset($this->session['logged_in'])) {
            return header("Location: " . self::BASE_URL);
        }

        // once again we include the paypal rest-api-sdk-php for the payment execution
        require __DIR__ . "/vendor/autoload.php";

        // In order to communicate with Paypal API we need to br authenticated with special credentials:
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AUA0vvs7vganK5I5g3EAJAgtpxxlt4WzlOK9MuMHDavYnWux5XkocZ2FaOfbzNBODM5kDf2LbeknZ5It',     // ClientID
                'EM-6C_MAmtbve4UrBMNib7tRfQEl-0spuGYubHhSUQK9fLWqa6R6p1Y1ongtE7VnXj29Mu8gFLfVARD3'      // ClientSecret
            )
        );

        $paymentId = $_GET['paymentId'];
        $payment = PayPal\Api\Payment::get($paymentId, $apiContext);

        $execution = new PayPal\Api\PaymentExecution();
        $execution->setPayerId($_GET['PayerID']);

        $transaction = new PayPal\Api\Transaction();
        $amount = new PayPal\Api\Amount();
        $details = new PayPal\Api\Details();

        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($this->session['cart_data']['total_price']);

        $amount->setCurrency('USD');

        $amount->setTotal($this->session['cart_data']['total_price']);

        $amount->setDetails($details);

        $transaction->setAmount($amount);

        $execution->addTransaction($transaction);

        $result = $payment->execute($execution, $apiContext);

        $result = $result->toJSON();

        $result = json_decode($result);

        // if the transaction is successfull
        if ($result->state === 'approved') {

            // // show all the payment info object
            // echo "<pre>";
            // print_r($result);    
            // echo "<pre>";
            
            // // show the payer details
            // echo $result->payer->payer_info->email; echo "<br>";
            // echo $result->payer->payer_info->first_name; echo "<br>";
            // echo $result->payer->payer_info->last_name; echo "<br>";
            // print_r($result->payer->payer_info->shipping_address) ; echo "<br>";
            // echo $result->transactions[0]->amount->total; echo "<br>";

            // echo "<pre>";
            // print_r($result->transactions[0]->item_list->items);    
            // echo "<pre>";

            // here we make sql query and save the order into the database
            // $mysqli = new mysqli("localhost", "my_user", "my_password", "world");
            // $sql =  "INSERT INTO table_orders (email, first_name, last_name, ...)
            //          VALUES (value1, value2, value3, ...); 
            // if ($result = $mysqli->query($sql)) {
            //    printf("Select returned %d rows.\n", $result->num_rows);
            //
            //    /* free result set */
            //   $result->close();
            // }

            $cart = new ShoppingCart($this->session);
            $cart->deleteCart();

        }

        return header("Location: " . self::BASE_URL);

    }



}

