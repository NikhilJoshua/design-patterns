<?php

namespace Strategy\RealWorld;

class OrderController {
    public function post(string $url, array $data) {
        echo 'Controller: POST request to $url with ' . json_encode($data) . '<br>';

        $path = parse_url($url, PHP_URL_PATH);

        if(preg_match('#^/orders$#', $path, $matches)){
            $this->postNewOrder($data);
        }
    }

    public function get(string $url): void {
        echo 'Controller: GET request to ' . $url .'<br>';
        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $data);

        if(preg_match('#^/orders?$#', $path, $matches)) {
            $this->getAllOrders();
        } else if (preg_match('#^/order/([0-9]+?)/payment/([a-z]+?)(/return)?$#', $path, $matches)) {
            $order = Order::get($matches[1]);

            $paymentMethod = PaymentFactory::getPaymentMethod($matches[2]);

            if(!isset($match[3])) {
                $this->getPayment($paymentMethod, $order, $data);
            }
            else {
                $this->getPaymentReturn($paymentMethod, $order, $data);
            }
        } else {
            echo 'Controller: 404 page<br>';
        }
    }

    public function postNewOrder(array $data): void {
        $order = new Order($data);
        echo 'Controller: Created the order #' . $order->id . '<br>';
    }

    public function getAllOrder(): void {
        echo 'Controller: Here\'s all orders:<br>';
        foreach(Order::get() as $order) {
            echo json_encode($order, JSON_PRETTY_PRINT) . '<br>';
        }
    }

    public function getPayment(PaymentMethod $method, Order $order, array $data){
        $form = $method->getPaymentForm($order);
        echo 'Controller: Here\'s the payment form:<br>';
        echo $form . '<br>';
    }

    public function getPaymentReturn(PaymentMethod $method, Order $order, array $data): void {
        try {
            if($method->validateReturn($order, $data)) {
                echo 'Controller: Thank for your order!<br>';
                $order->complete();
            }
        } catch (\Exception $e) {
            echo 'Controller: got an exception(' . $e->getMessage() . ')<br>';
        }
    }
}

class Order {
    private static $orders = [];

    public static function get(int $orderId = null) {
        if($orderId === null) {
            return static::$orders;
        } else {
            return static::$orders[$orderId];
        }
    }

    public function __construct(array $attributes) {
        $this->id = count(static::$orders);
        $this->status = 'new';
        foreach($attributes as $key => $value) {
            $this->{$key} = $value;

        }

        static::$orders[$this->id] = $this;
    }

    public function complete(): void {
        $this->status = 'completed';
        echo 'Order: #' . $this->id . 'is now ' . $this->status;
    }
}

class PaymentFactory {
    public static function getPaymentMethod(string $id): PaymentMethod {
        switch($id) {
            case 'cc':
                return new CreditCardPayment();
            case 'paypal':
                return new PayPalPayment();
            default:
                throw new \Exception('Unknown Payment Method');
        }

    }
}

interface PaymentMethod {
    public function getPaymentForm(Order $order): string;

    public function validateReturn(Order $order, array $data): bool;
}

class CreditCardPayment implements PaymentMethod {
    static private $store_secret_key = 'swordfish';

    public function getPaymentForm(Order $order): string {
        $returnURL = 'https://nikhiljoshua.com/' . 'order/' . $order->id . '/payment/cc/return';
        return <<<FORM
    <from action="https://my-credit-card-processor.com/charge" method="POST">
    <input type="hidden" id="email" value="{$order->email}">
    <input type="hidden" id="total" value="{$order->total}">
    <input type="hidden" id="returnURL" value="$returnURL">
    <input type="text" id="cardholder-name">
    <input type="text" id="credit-card">
    <input type="text" id="expiration-date">
    <input type="text" id="cvv-number">
    <input type="submit" value="Pay">
</from>
FORM;
    }
    public function validateReturn(Order $order, array $data): bool {
        echo 'CreditCardPayment: ...validating...';

        if($data['key'] != md5($order->id . static::$store_secret_key)) {
            throw new \Exception('Payment key is wrong');
        }

        if(!isset($data['success']) || !$data['success'] || $data['success'] == 'false') {
            throw new \Exception(('Payment Failed'));
        }
        echo 'Done!<br>';

        return true;
    }
}

class PayPalPayment implements PaymentMethod {
    public function getPaymentForm(Order $order): string {
        $returnURL = 'https://nikhiljoshua.com/' . 'order/' . $order->id . '/payment/paypal/return';

        return <<<FORM
<form action="https://paypal.com/payment" method="POST">
    <input type="hidden" id="email" value="{$order->email}">
    <input type="hidden" id="total" value="{$order->total}">
    <input type="hidden" id="returnURL" value="$returnURL">
    <input type="submit" value="Pay on PayPal">
</form>
FORM;
    }

    public function validateReturn(Order $order, array $data): bool {
        echo 'PayPalPayment: ...validationg.';

        echo 'Done!\n';
        return true;
    }
}

$controller = new ORderController();

echo 'Client: Let\'s create some orders <br>';

$controller->post("/orders", [
    'email' => 'nikhil.joshua@rtcamp.com',
    'product' => 'Pizza',
    'total' => 3.4,
]);

$controller->post('/order', [
    'email' => 'nikhil.joshua@rtcamp.com',
    'product' => 'something',
    'total' => 10.9
]);

echo 'Client: List my orders, please<br>';

$controller->get('/orders');

echo '<br>Client: I\'d like to pay for the second, show me the payment form<br>';

$controller->get('/order/1/payment/paypal');

echo '<br>Client: ...pushes the Pay button...<br>';
echo 'Client: Oh, I\'m redirected to the paypal. <br>';
echo '<br>Client: ..pays on the PayPal..<br>';
echo '\nClient: Alright, I\'m back with you, guys <br>';

$controller->get('order/1/payment/paypal/return' . '?key=c3334dasd3kjadf');