<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cake_id = filter_input(INPUT_POST, 'cake_id', FILTER_VALIDATE_INT);
    $cake_name = filter_input(INPUT_POST, 'cake_name', FILTER_SANITIZE_STRING);
    $cake_pound = filter_input(INPUT_POST, 'cake_pound', FILTER_VALIDATE_INT);
    $customer_name = filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_STRING);
    $customer_phone = filter_input(INPUT_POST, 'customer_phone', FILTER_SANITIZE_STRING);
    $delivery_date = filter_input(INPUT_POST, 'delivery_date', FILTER_SANITIZE_STRING);
    $special_instructions = filter_input(INPUT_POST, 'special_instructions', FILTER_SANITIZE_STRING);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $is_quotation = isset($_POST['is_quotation']) ? 1 : 0;

    // Validate inputs
    if (!$cake_id || !$cake_name || !$cake_pound || !$customer_name || !$customer_phone || !$delivery_date || !$quantity) {
        die('Invalid input data.');
    }

    // Fetch cake details
    $stmt = $pdo->prepare("SELECT base_price, stock FROM cakes WHERE id = ?");
    $stmt->execute([$cake_id]);
    $cake = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cake || $cake['stock'] === 'out') {
        die('Cake is out of stock or unavailable.');
    }

    $total_price = $cake['base_price'] * $cake_pound;

    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (cake_id, cake_name, cake_pound, total_price, customer_name, customer_phone, delivery_date, quantity, special_instructions, is_quotation, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $status = $is_quotation ? 'quotation' : 'pending';
    $stmt->execute([
        $cake_id,
        $cake_name,
        $cake_pound,
        $total_price,
        $customer_name,
        $customer_phone,
        $delivery_date,
        $quantity,
        $special_instructions,
        $is_quotation,
        $status
    ]);

    // Set confirmation message
    $message = $is_quotation
        ? "Thank you, $customer_name! Your quotation request for $quantity $cake_pound-pound $cake_name has been submitted."
        : "Thank you, $customer_name! Your order for $quantity $cake_pound-pound $cake_name has been placed. Total: " . number_format($total_price * $quantity, 2) . " BDT";
    $_SESSION['confirmation_message'] = $message;
    $_SESSION['notification_type'] = $is_quotation ? 'quotation' : 'order';

    header('Location: index.php#home');
    exit;
}
?>