<?php
require_once '../config/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date'])) {
    $date = $_POST['date'];
    $bookedSlots = getBookedSlots($db, $date);
    
    // Generate all time slots
    $allSlots = generateTimeSlots();
    $slots = [];
    
    foreach ($allSlots as $slot) {
        $available = isSlotAvailable($db, $date, $slot);
        $slots[] = [
            'time' => $slot,
            'available' => $available
        ];
    }
    
    echo json_encode($slots);
}
?>