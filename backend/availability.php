<?php

declare(strict_types=1);

$database = new PDO('sqlite:' . __DIR__ . '/database/database.db');
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$roomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : null;

// If no room selected → no unavailable dates
if (!$roomId) {
    echo json_encode([]);
    exit;
}

/*
 Fetch bookings for January only
 NOTE: departure_date is NOT blocked (checkout day is free)
*/
$stmt = $database->prepare("
    SELECT arrival_date, departure_date
    FROM bookings
    WHERE room_id = :room
      AND arrival_date < '2026-02-01'
      AND departure_date > '2026-01-01'
");

$stmt->execute([':room' => $roomId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
 Expand date ranges into individual blocked days
*/
$blocked = [];

foreach ($bookings as $b) {
    $start = new DateTime($b['arrival_date']);
    $end   = new DateTime($b['departure_date']);

    while ($start < $end) {
        $blocked[] = $start->format('Y-m-d');
        $start->modify('+1 day');
    }
}

echo json_encode(array_values(array_unique($blocked)));
