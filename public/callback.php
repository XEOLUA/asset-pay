<?php
//    $json = json_decode(file_get_contents('php://input'), true);
//
//    $key = $this->config->get('payment_assetpayments_merchant');
//    $secret = $this->config->get('payment_assetpayments_signature');
//    $transactionId = $json['Payment']['TransactionId'];
//    $signature = $json['Payment']['Signature'];
//    $order_id = $json['Order']['OrderId'];
//    $status = $json['Payment']['StatusCode'];
//
//    $requestSign =$key.':'.$transactionId.':'.strtoupper($secret);
//    $sign = hash_hmac('md5',$requestSign,$secret);
//
//    if ($status == 1 && $sign == $signature) {
//        $this->load->model('checkout/order');
//        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_assetpayments_order_status_id'),'AssetPayments TransactionID: ' .$transactionId);
//    }
//    if ($status == 2 && $sign == $signature) {
//        $this->load->model('checkout/order');
//        $this->model_checkout_order->addOrderHistory($order_id, 1,'Payment FAILED TransactionID: ' .$transactionId);
//    }

    echo 1;
