<?php
// admin.php - रेस्टोरेंट ऑनर के लिए ऑर्डर देखने का पैनल
session_start();

// सिंपल लॉगिन लॉजिक
$correct_password = 'admin123'; // इसे बदल कर मजबूत पासवर्ड रखें

if (isset($_POST['logout'])) {
    unset($_SESSION['admin_logged_in']);
}

if (isset($_POST['password']) && $_POST['password'] === $correct_password) {
    $_SESSION['admin_logged_in'] = true;
}

// अगर लॉगिन नहीं है तो लॉगिन फॉर्म दिखाएं
if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Admin Login</title><link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"></head>
    <body style="font-family: Poppins; background: #fef9f2; display: flex; justify-content: center; align-items: center; height: 100vh;">
        <form method="POST" style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
            <h2 style="color:#d35400;">रेस्टोरेंट लॉगिन</h2>
            <input type="password" name="password" placeholder="पासवर्ड डालें" style="padding: 12px; width: 100%; margin: 15px 0; border-radius: 30px; border: 2px solid #f1c40f;">
            <button type="submit" style="background: #d35400; color: white; border: none; padding: 12px; width: 100%; border-radius: 30px; font-weight: bold;">लॉगिन करें</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

// लॉगिन सफल - ऑर्डर दिखाएं
$orders_file = 'orders.json';
$orders = [];
if (file_exists($orders_file)) {
    $content = file_get_contents($orders_file);
    $orders = json_decode($content, true) ?? [];
}
// नए ऑर्डर ऊपर दिखाने के लिए रिवर्स करें
$orders = array_reverse($orders);
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>एडमिन पैनल | स्वाद रेस्टोरेंट</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f6fa; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #d35400; }
        .logout { float: right; background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 30px; cursor: pointer; }
        .order-card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 8px solid #e67e22; }
        .order-header { display: flex; justify-content: space-between; border-bottom: 2px solid #f1c40f; padding-bottom: 10px; margin-bottom: 15px; }
        .order-id { font-weight: bold; font-size: 1.2rem; color: #a04000; }
        .order-time { color: #7f8c8d; }
        .customer-details { background: #fdf2e9; padding: 15px; border-radius: 15px; margin-bottom: 15px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table th { background: #f1c40f; padding: 10px; text-align: left; }
        .items-table td { padding: 10px; border-bottom: 1px solid #ecf0f1; }
        .total-row { font-weight: bold; text-align: right; font-size: 1.2rem; }
        .status-btn { background: #2ecc71; color: white; border: none; padding: 8px 20px; border-radius: 30px; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST" style="display: flex; justify-content: space-between; align-items: center;">
            <h1>📋 ऑर्डर लिस्ट (कुल <?php echo count($orders); ?>)</h1>
            <button type="submit" name="logout" class="logout">लॉगआउट</button>
        </form>
        <p style="color:#7f8c8d; margin-bottom: 20px;">ऑटो-रिफ्रेश के लिए पेज रीलोड करें।</p>
        
        <?php if (empty($orders)): ?>
            <p style="text-align: center; background: white; padding: 40px; border-radius: 20px;">अभी तक कोई ऑर्डर नहीं आया है।</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <span class="order-id">#<?php echo $order['order_id']; ?></span>
                    <span class="order-time"><?php echo $order['datetime']; ?></span>
                </div>
                <div class="customer-details">
                    <strong>👤 <?php echo $order['customer']['name']; ?></strong> | 
                    📞 <?php echo $order['customer']['phone']; ?><br>
                    📍 <?php echo $order['customer']['address']; ?>
                    <?php if (!empty($order['customer']['instructions'])): ?>
                        <br>💬 निर्देश: <?php echo $order['customer']['instructions']; ?>
                    <?php endif; ?>
                </div>
                <table class="items-table">
                    <tr><th>आइटम</th><th>मात्रा</th><th>कीमत</th></tr>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row"><td colspan="3">कुल: ₹<?php echo $order['total']; ?></td></tr>
                </table>
                <button class="status-btn" onclick="alert('ग्राहक को कॉल करें: <?php echo $order['customer']['phone']; ?>')">📞 कॉल करें</button>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <p style="margin-top: 20px; text-align: center;">🔒 एडमिन पैनल - स्वाद रेस्टोरेंट</p>
    </div>
</body>
</html>