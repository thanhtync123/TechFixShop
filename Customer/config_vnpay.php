<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');


$vnp_TmnCode = "6EDNS6NT"; 
$vnp_HashSecret ="J1EZA9K11FM2EVTFK0VB1N0A4EX3W8X9"; 
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://localhost:8080/TechFixPHP/Customer/vnpay_return.php"; 
$vnp_apiUrl = "https://sandbox.vnpayment.vn/merchantv2/";

$startTime = date("YmdHis");
$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
?>