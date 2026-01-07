<?php

declare(strict_types=1);
session_start();

$database = new PDO('sqlite:' . __DIR__ . '/backend/database/database.db');
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*
 Fetch rooms
*/
$roomsStmt = $database->query("
    SELECT id, tier, price_per_night, description
    FROM rooms
    ORDER BY price_per_night ASC
");
$rooms = array_reverse($roomsStmt->fetchAll(PDO::FETCH_ASSOC));

/*
 Fetch unlocked features
*/
$stmt = $database->query("
    SELECT 
        f.id,
        f.category,
        f.tier,
        f.feature_name,
        f.price
    FROM features f
    JOIN hotel_features hf ON hf.feature_id = f.id
    ORDER BY f.category, f.id
");
$features = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
 Group features
*/
$featuresByCategory = [];
foreach ($features as $feature) {
    $featuresByCategory[$feature['category']][] = $feature;
}

/*
 Status messages
*/
$status = $_GET['status'] ?? null;

$statusMessages = [
    'success' => 'Booking successful!',
    'transfer_expired' => 'Transfer code expired.',
    'transfer_invalid' => 'Transfer code invalid.',
    'departure_date_error' => 'Departure cannot be before arrival.',
    'room_unavailable' => 'Selected room is not available for those dates.',
    'error' => 'Something went wrong.'
];

$showModal = $status && isset($statusMessages[$status]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FourWalls</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Notable&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="frontend/styles/styles.css">

    <!-- Minimal modal styling (does NOT affect layout) -->
    <style>
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-box {
            background: #fff;
            padding: 2rem;
            max-width: 420px;
            width: 90%;
            position: relative;
            border-radius: 6px;
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 14px;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
</head>

<body>

    <section class="hotelWrapper">

        <div id="hotel"></div>

        <form action="/backend/booking.php" method="post" class="booking">

            <label>Your name</label>
            <input type="text" name="guestId" required>

            <label>Transfer code</label>
            <input type="text" name="transferCode" required>

            <!-- ROOMS -->
            <div class="room">

                <div class="room_type">
                    <h2>Room Type</h2>

                    <?php foreach ($rooms as $room): ?>
                        <label class="room-option">
                            <input
                                type="radio"
                                name="room"
                                value="<?= (int)$room['id'] ?>"
                                data-room-id="<?= (int)$room['id'] ?>"
                                data-room-tier="<?= htmlspecialchars($room['tier']) ?>"
                                data-room-price="<?= (float)$room['price_per_night'] ?>"
                                required>

                            <span class="room-name"><?= ucfirst($room['tier']) ?></span>
                            <span class="room-price">$<?= number_format($room['price_per_night'], 2) ?></span>
                        </label>
                    <?php endforeach; ?>

                    <p class="room-description" id="roomDescription"></p>
                </div>

                <div class="calendarWrapper">
                    <div class="calendar">
                        <h2>January</h2>
                        <div class="calendar-grid" id="calendarGrid"></div>
                    </div>

                    <label>Arrival</label>
                    <input type="date" name="arrival" min="2026-01-01" max="2026-01-31" required>

                    <label>Departure</label>
                    <input type="date" name="departure" min="2026-01-02" max="2026-02-01" required>
                </div>

            </div>

            <!-- FEATURES -->
            <h3>Features</h3>

            <section class="featureWrapper">
                <?php foreach ($featuresByCategory as $category => $features): ?>
                    <div class="featureCategory feature-category-<?= htmlspecialchars($category) ?>">
                        <p class="feature-title"><?= ucfirst($category) ?></p>

                        <?php foreach ($features as $feature): ?>
                            <label class="feature-tier-<?= htmlspecialchars($feature['tier']) ?>">
                                <input
                                    type="checkbox"
                                    name="features[]"
                                    value="<?= (int)$feature['id'] ?>"
                                    data-price="<?= (float)$feature['price'] ?>"
                                    data-feature-name="<?= htmlspecialchars($feature['feature_name']) ?>">
                                <?= ucfirst(htmlspecialchars($feature['feature_name'])) ?>
                                (<?= ucfirst($feature['tier']) ?>,
                                $<?= number_format($feature['price'], 1) ?>)
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </section>

            <p>Total price: <strong id="livePrice">$0.00</strong></p>

            <button type="submit">Book now</button>

        </form>

        <!-- MODAL (SUCCESS + ERRORS) -->
        <?php if ($showModal): ?>
            <div class="modal-overlay" onclick="this.remove()">
                <div class="modal-box" onclick="event.stopPropagation()">
                    <span class="modal-close" onclick="this.closest('.modal-overlay').remove()">✕</span>

                    <h2><?= htmlspecialchars($statusMessages[$status]) ?></h2>

                    <?php if ($status === 'success' && isset($_SESSION['receipt'])): ?>
                        <p><strong>Guest:</strong> <?= htmlspecialchars($_SESSION['receipt']['guest'] ?? '') ?></p>
                        <p><strong>Room:</strong> <?= htmlspecialchars($_SESSION['receipt']['room'] ?? '') ?></p>
                        <p><strong>Nights:</strong> <?= (int)($_SESSION['receipt']['nights'] ?? 1) ?></p>

                        <?php if (!empty($_SESSION['receipt']['features'])): ?>
                            <p><strong>Features:</strong>
                                <?= htmlspecialchars(implode(', ', $_SESSION['receipt']['features'])) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($_SESSION['receipt']['discount'])): ?>
                            <p><strong>Discount:</strong>
                                -$<?= number_format((float)$_SESSION['receipt']['discount'], 2) ?>
                            </p>
                        <?php endif; ?>

                        <p><strong>Total:</strong>
                            $<?= number_format((float)$_SESSION['receipt']['total'], 2) ?>
                        </p>
                    <?php endif; ?>

                </div>
            </div>
            <?php unset($_SESSION['receipt']); ?>
        <?php endif; ?>

    </section>

    <script>
        const ROOM_DESCRIPTIONS = <?= json_encode(
                                        array_column($rooms, 'description', 'tier'),
                                        JSON_THROW_ON_ERROR
                                    ) ?>;
    </script>

    <script src="frontend/scripts/hotel.js"></script>

</body>

</html>