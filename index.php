<?php

declare(strict_types=1);
require(__DIR__ . "/backend/vendor/autoload.php");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yrgopelago</title>
    <link rel="stylesheet" href="frontend/styles/styles.css">


</head>

<body>

    <form action="/backend/booking.php" method="post" class="booking">

        <label for="guestId" class="block mt-3">Your name (guest_id)</label>
        <input type="text" name="guestId" class="form-input" required="">

        <label for="transferCode" class="block mt-3">transferCode</label>
        <input type="text" name="transferCode" class="form-input">


        <label for="arrival" class="block mt-3">Arrival</label>
        <input type="date" name="arrival" class="form-input" min="2026-01-01" max="2026-01-31">

        <label for="departure" class="block mt-3">Departure</label>
        <input type="date" name="departure" class="form-input" min="2026-01-01" max="2026-01-31">

        <label for="room" class="block mt-3">Room</label>
        <select name="room" id="" class="form-input pr-12">
            <option value="1">Economy</option>
            <option value="2">Standard</option>
            <option value="3">Luxury</option>
        </select>


        <br>
        <label for="features" class="block mt-6">Features</label>

        <div class="featureCategory">
            <p>Water</p>

            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="">
                Pool (Economy, $0.5)
            </label>

            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="">
                Scuba Diving (Basic, $1)
            </label>

            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="">
                Olympic Pool (Premium, $1.5)
            </label>

            <label class="block ml-2">
                <input class="mr-2" type="checkbox" name="features[]" value="">
                waterpark with fire and minibar (superior, $2)
            </label>
        </div>


        <button name="submit" type="submit">Book your visit now!</button>











        <scrpt src="/frontend/scripts/main.js"></scrpt>
</body>

</html>