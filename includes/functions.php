<?php
// Get all active themes
function getThemes($db) {
    $query = "SELECT * FROM themes WHERE status = 'active' ORDER BY price ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get theme by ID
function getThemeById($db, $id) {
    $query = "SELECT * FROM themes WHERE id = ? AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Get all active add-ons
function getAddons($db) {
    $query = "SELECT * FROM addons WHERE status = 'active' ORDER BY price ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get booked slots for a specific date
function getBookedSlots($db, $date) {
    $query = "SELECT start_time, end_time FROM booked_slots 
              WHERE booking_date = ? AND status = 'booked'";
    $stmt = $db->prepare($query);
    $stmt->execute([$date]);
    return $stmt->fetchAll();
}

// Generate time slots
function generateTimeSlots() {
    $slots = [];
    $start = strtotime('11:20');
    $end = strtotime('22:45');
    
    while ($start <= $end) {
        $slot_start = date('H:i', $start);
        $slot_end = date('H:i', strtotime('+20 minutes', $start));
        $slots[] = $slot_start . ' - ' . $slot_end;
        $start = strtotime('+20 minutes', $start);
    }
    
    return $slots;
}

// Check if time slot is available
function isSlotAvailable($db, $date, $time) {
    $bookedSlots = getBookedSlots($db, $date);
    
    foreach ($bookedSlots as $slot) {
        $booked_start = strtotime($slot['start_time']);
        $booked_end = strtotime($slot['end_time']);
        $check_time = strtotime(explode(' - ', $time)[0]);
        
        if ($check_time >= $booked_start && $check_time < $booked_end) {
            return false;
        }
    }
    
    return true;
}

// Validate coupon
function validateCoupon($db, $code, $totalAmount) {
    $query = "SELECT * FROM coupons WHERE code = ? AND status = 'active' 
              AND (valid_from IS NULL OR valid_from <= CURDATE())
              AND (valid_until IS NULL OR valid_until >= CURDATE())
              AND (max_usage = 0 OR used_count < max_usage)
              AND min_purchase <= ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$code, $totalAmount]);
    return $stmt->fetch();
}

// Calculate discount
function calculateDiscount($coupon, $totalAmount) {
    if (!$coupon) return 0;
    
    if ($coupon['discount_type'] == 'fixed') {
        return $coupon['discount_value'];
    } else {
        return ($totalAmount * $coupon['discount_value']) / 100;
    }
}

// Save booking to database
function saveBooking($db, $bookingData) {
    try {
        $db->beginTransaction();
        
        // Insert booking
        $query = "INSERT INTO bookings (
            booking_reference, theme_id, booking_date, booking_time, duration,
            customer_name, customer_phone, customer_email, pax_count,
            base_price, discount, addons_total, total_price, deposit, balance,
            coupon_code, slot_expires_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $bookingData['reference'],
            $bookingData['theme_id'],
            $bookingData['date'],
            $bookingData['time'],
            $bookingData['duration'],
            $bookingData['customer_name'],
            $bookingData['customer_phone'],
            $bookingData['customer_email'],
            $bookingData['pax'],
            $bookingData['base_price'],
            $bookingData['discount'],
            $bookingData['addons_total'],
            $bookingData['total_price'],
            $bookingData['deposit'],
            $bookingData['balance'],
            $bookingData['coupon_code'],
            $bookingData['slot_expires_at']
        ]);
        
        $booking_id = $db->lastInsertId();
        
        // Insert add-ons if any
        if (!empty($bookingData['addons'])) {
            $addon_query = "INSERT INTO booking_addons (booking_id, addon_id, quantity, price) 
                           VALUES (?, ?, ?, ?)";
            $addon_stmt = $db->prepare($addon_query);
            
            foreach ($bookingData['addons'] as $addon) {
                $addon_stmt->execute([
                    $booking_id,
                    $addon['id'],
                    $addon['quantity'],
                    $addon['price']
                ]);
            }
        }
        
        // Book the time slot
        $time_parts = explode(' - ', $bookingData['time']);
        $slot_query = "INSERT INTO booked_slots (booking_date, start_time, end_time, booking_id) 
                      VALUES (?, ?, ?, ?)";
        $slot_stmt = $db->prepare($slot_query);
        $slot_stmt->execute([
            $bookingData['date'],
            $time_parts[0],
            $time_parts[1],
            $booking_id
        ]);
        
        $db->commit();
        return $booking_id;
        
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}
?>