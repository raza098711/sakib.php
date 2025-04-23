<?php
session_start();
require 'db_connect.php';

// Fetch settings
$settings = [];
$stmt = $pdo->query("SELECT key_name, value FROM settings");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key_name']] = $row['value'];
}

// Fetch cakes
$cakes = $pdo->query("SELECT * FROM cakes WHERE available = 1")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Delights - Cake Order</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header id="headerContent">
        <div class="logo">
            <h1>Sweet Delights</h1>
            <p>Order delicious cakes online</p>
        </div>
        <nav>
            <ul>
                <li><a href="#home" class="active">Home</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="http://localhost/cake-order-admin" class="btn">Admin</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="home" class="hero">
            <div class="hero-content" id="heroContent"><?php echo $settings['hero_content']; ?></div>
        </section>

        <section id="menu" class="menu-section">
            <h2 id="menuTitle"><?php echo htmlspecialchars($settings['menu_title']); ?></h2>
            <div class="cake-grid" id="cakeGrid">
                <?php foreach ($cakes as $cake): ?>
                    <div class="cake-card animate-fade">
                        <div class="cake-img">
                            <img src="<?php echo htmlspecialchars($cake['image']); ?>"
                                alt="<?php echo htmlspecialchars($cake['name']); ?>">
                            <?php if ($cake['stock'] === 'out'): ?>
                                <span class="stock-out">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        <div class="cake-info">
                            <h3><?php echo htmlspecialchars($cake['name']); ?></h3>
                            <p class="cake-price"><?php echo number_format($cake['base_price'], 2); ?> BDT per pound</p>
                            <p class="cake-desc"><?php echo htmlspecialchars($cake['description']); ?></p>
                            <?php if ($cake['stock'] !== 'out'): ?>
                                <button class="btn order-btn" data-cake="<?php echo htmlspecialchars($cake['name']); ?>"
                                    data-cake-id="<?php echo $cake['id']; ?>">Order Now</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="orderForm" class="order-form hidden">
            <div class="form-container">
                <button id="closeFormBtn" class="close-btn">×</button>
                <h2 id="orderFormTitle"><?php echo htmlspecialchars($settings['order_form_title']); ?></h2>
                <form id="cakeOrderForm" action="process_order.php" method="POST">
                    <input type="hidden" id="cakeId" name="cake_id">
                    <div class="form-group">
                        <label for="cakeName"
                            id="cakeNameLabel"><?php echo htmlspecialchars($settings['cake_name_label']); ?></label>
                        <input type="text" id="cakeName" name="cake_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="cakePound"
                            id="cakePoundLabel"><?php echo htmlspecialchars($settings['cake_pound_label']); ?></label>
                        <select id="cakePound" name="cake_pound" required>
                            <option value="1">1 Pound</option>
                            <option value="2">2 Pounds</option>
                            <option value="3">3 Pounds</option>
                            <option value="4">4 Pounds</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="customerName"
                            id="customerNameLabel"><?php echo htmlspecialchars($settings['customer_name_label']); ?></label>
                        <input type="text" id="customerName" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="customerPhone"
                            id="customerPhoneLabel"><?php echo htmlspecialchars($settings['customer_phone_label']); ?></label>
                        <input type="tel" id="customerPhone" name="customer_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="deliveryDate"
                            id="deliveryDateLabel"><?php echo htmlspecialchars($settings['delivery_date_label']); ?></label>
                        <input type="date" id="deliveryDate" name="delivery_date" required>
                    </div>
                    <div class="form-group">
                        <label for="specialInstructions"
                            id="specialInstructionsLabel"><?php echo htmlspecialchars($settings['special_instructions_label']); ?></label>
                        <textarea id="specialInstructions" name="special_instructions" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="quantity"
                            id="quantityLabel"><?php echo htmlspecialchars($settings['quantity_label']); ?></label>
                        <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="isQuotation"
                            id="isQuotationLabel"><?php echo htmlspecialchars($settings['is_quotation_label']); ?></label>
                        <input type="checkbox" id="isQuotation" name="is_quotation">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Place Order</button>
                        <button type="button" class="btn cancel" id="cancelOrderBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </section>

        <section id="about" class="about-section">
            <h2 id="aboutTitle"><?php echo htmlspecialchars($settings['about_title']); ?></h2>
            <div id="aboutContent" contenteditable="false"><?php echo $settings['about_content']; ?></div>
        </section>

        <section id="contact" class="contact-section">
            <h2 id="contactTitle"><?php echo htmlspecialchars($settings['contact_title']); ?></h2>
            <div id="contactContent" contenteditable="false" class="contact-info">
                <?php echo $settings['contact_content']; ?></div>
        </section>

        <div id="orderConfirmation" class="modal hidden">
            <div class="modal-content">
                <span class="close">×</span>
                <h2>Order Confirmation</h2>
                <p id="confirmationMessage"></p>
                <button id="confirmCloseBtn" class="btn">Close</button>
            </div>
        </div>
    </main>

    <footer id="footerContent"><?php echo $settings['footer_content']; ?></footer>

    <script src="scripts.js"></script>
</body>

</html>