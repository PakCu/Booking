<?php
session_start();

// Base URL
define('BASE_URL', 'http://localhost/fl/spd/');

// Database connection
require_once 'database.php';
$database = new Database();
$db = $database->connect();

// Timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

// Deposit amount
define('DEPOSIT_AMOUNT', 50);

// Slot lock duration (in minutes)
define('SLOT_LOCK_DURATION', 10);

// Helper function
function redirect($page) {
    header("Location: " . BASE_URL . $page);
    exit();
}

function generateBookingReference() {
    return 'SPD' . date('Ymd') . rand(1000, 9999);
}

function calculateSlotExpiry() {
    return date('Y-m-d H:i:s', strtotime('+' . SLOT_LOCK_DURATION . ' minutes'));
}
?>