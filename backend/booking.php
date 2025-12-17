<?php

if (isset($_POST['guestId'], $_POST['transferCode'], $_POST['arrival'], $_POST['departure'], $_POST['room'])) {

    $guestId = $_POST['guestId'];
    $transferCode = $_POST['transferCode'];
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];
    $room = $_POST['room'];
};

var_dump($guestId);
