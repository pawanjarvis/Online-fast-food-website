<?php
// save_order.php - ऑर्डर सेव करके कन्फर्मेशन दिखाता है

// अगर कोई POST रिक्वेस्ट नहीं है तो वापस भेज दें
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: restaurant.html');
    exit;
}

// फॉर्म डेटा लें
$name = htmlspecialchars(trim($_POST['customer_name'] ?? ''));
$phone = htmlspecialchars(trim($_POST['customer_phone'] ?? ''));
$address = htmlspecialchars(trim($_POST['customer_address'] ?? ''));
$instructions = htmlspecialchars(trim($_POST['special_instructions'] ?? ''));
$cart_json = $_POST['cart_data'] ?? '[]';
$cart = json_decode($cart_json, true);

// बेसिक वैलिडेशन
if (empty($name) || empty($phone) || empty($address) || empty($cart)) {
    die('कृपया सभी ज़रूरी जानकारी भरें। <a href="checkout.html">वापस जाएं</a>');
}

// कुल राशि निकालें
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// एक यूनिक ऑर्डर आईडी बनाएं
$order_id = 'SWD' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
$order_date = date('Y-m-d H:i:s');

// ऑर्डर डेटा ऐरे में तैयार करें
$order_data = [
    'order_id' => $order_id,
    'datetime' => $order_date,
    'customer' => [
        'name' => $name,
        'phone' => $phone,
        'address' => $address,
        'instructions' => $instructions
    ],
    'items' => $cart,
    'total' => $total
];

// डेटा को JSON फाइल में सेव करें (डेटाबेस की जगह - शुरुआत के लिए आसान)
$orders_file = 'orders.json';

// पुराने ऑर्डर पढ़ें
$all_orders = [];
if (file_exists($orders_file)) {
    $json_content = file_get_contents($orders_file);
    if (!empty($json_content)) {
        $all_orders = json_decode($json_content, true) ?? [];
    }
}

// नया ऑर्डर जोड़ें
$all_orders[] = $order_data;

// वापस JSON फाइल में सेव करें
$success = file_put_contents($orders_file, json_encode($all_orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($success === false) {
    die('ऑर्डर सेव करने में त्रुटि। कृपया रेस्टोरेंट पर कॉल करें।');
}

// कन्फर्मेशन पेज दिखाएं
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ऑर्डर कन्फर्म | स्वाद रेस्टोरेंट</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #fef9f2; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .confirmation-box { background: white; max-width: 600px; padding: 40px; border-radius: 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.1); text-align: center; }
        .checkmark { font-size: 5rem; color: #27ae60; margin-bottom: 20px; }
        h1 { color: #d35400; margin-bottom: 15px; }
        .order-id { background: #fdf2e9; padding: 10px 20px; border-radius: 40px; display: inline-block; margin: 20px 0; font-size: 1.5rem; font-weight: bold; color: #a04000; }
        p { margin-bottom: 10px; color: #2c3e50; }
        .btn-home { background: #d35400; color: white; text-decoration: none; padding: 12px 30px; border-radius: 40px; display: inline-block; margin-top: 20px; font-weight: 600; }
        .btn-home:hover { background: #a04000; }
    </style>
</head>
<body>
    <div class="confirmation-box">
        <div class="checkmark">✅</div>
        <h1>ऑर्डर कन्फर्म हो गया!</h1>
        <p>धन्यवाद <?php echo $name; ?> जी, आपका ऑर्डर हमें मिल गया है।</p>
        <div class="order-id">ऑर्डर आईडी: <?php echo $order_id; ?></div>
        <p><strong>कुल राशि:</strong> ₹<?php echo $total; ?></p>
        <p>हमारी टीम जल्द ही आपको <?php echo $phone; ?> पर कॉल करके ऑर्डर कन्फर्म करेगी।</p>
        <p>अनुमानित डिलीवरी समय: 30-45 मिनट</p>
        <a href="restaurant.html" class="btn-home">🍽️ मेन्यू पर वापस जाएं</a>
    </div>
</body>
</html>