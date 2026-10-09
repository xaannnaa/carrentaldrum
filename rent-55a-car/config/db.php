<?php
/**
 * Konekcija ka bazi podataka
 * Po potrebi izmijeni podatke ispod (podrazumijevano za XAMPP).
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'drum_rentacar');

$konekcija = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$konekcija) {
    die('Greška pri povezivanju na bazu: ' . mysqli_connect_error());
}

mysqli_set_charset($konekcija, 'utf8mb4');

// Sesija se pokreće na jednom mjestu za cijelu aplikaciju
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
