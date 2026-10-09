<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

zahtijevajPrijavu();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    preusmjeri('cars.php');
}

$voziloId = (int)($_POST['vozilo_id'] ?? 0);
$datumOd = $_POST['datum_preuzimanja'] ?? '';
$datumDo = $_POST['datum_vracanja'] ?? '';
$mjesto = trim($_POST['mjesto_preuzimanja'] ?? '');
$napomena = trim($_POST['napomena'] ?? '');

// ---- Validacija ----
$greske = [];

if ($voziloId <= 0) $greske[] = 'Vozilo nije ispravno označeno.';
if (!$datumOd || !$datumDo) $greske[] = 'Moraš unijeti oba datuma.';
if ($mjesto === '') $greske[] = 'Moraš unijeti mjesto preuzimanja.';

if ($datumOd && $datumDo) {
    $danas = date('Y-m-d');
    if ($datumOd < $danas) $greske[] = 'Datum preuzimanja ne može biti u prošlosti.';
    if ($datumDo <= $datumOd) $greske[] = 'Datum vraćanja mora biti poslije datuma preuzimanja.';
}

if ($greske) {
    $_SESSION['poruka_greska'] = implode(' ', $greske);
    preusmjeri('car-details.php?id=' . $voziloId);
}

// ---- Provjeri da vozilo postoji i dostupno je ----
$stmt = mysqli_prepare($konekcija, "SELECT * FROM vozila WHERE id = ? AND dostupno = 1");
mysqli_stmt_bind_param($stmt, 'i', $voziloId);
mysqli_stmt_execute($stmt);
$vozilo = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$vozilo) {
    $_SESSION['poruka_greska'] = 'Ovo vozilo trenutno nije dostupno za rezervaciju.';
    preusmjeri('cars.php');
}

// ---- Provjeri preklapanje sa postojećim rezervacijama (bez obzira na korisnika) ----
$stmtPreklapanje = mysqli_prepare($konekcija, "
    SELECT COUNT(*) AS broj FROM rezervacije
    WHERE vozilo_id = ?
    AND status IN ('na cekanju','potvrdjena')
    AND NOT (datum_vracanja <= ? OR datum_preuzimanja >= ?)
");
mysqli_stmt_bind_param($stmtPreklapanje, 'iss', $voziloId, $datumOd, $datumDo);
mysqli_stmt_execute($stmtPreklapanje);
$preklapanje = mysqli_stmt_get_result($stmtPreklapanje)->fetch_assoc();

if ($preklapanje['broj'] > 0) {
    $_SESSION['poruka_greska'] = 'Vozilo je već rezervisano u izabranom periodu. Pokušaj sa drugim datumima.';
    preusmjeri('car-details.php?id=' . $voziloId);
}

// ---- Izračunaj cijenu na serveru (nikad ne vjeruj cijeni sa klijenta) ----
$dana = brojDana($datumOd, $datumDo);
$ukupnaCijena = $dana * $vozilo['cijena_po_danu'];

// ---- Upiši rezervaciju ----
$stmtUpis = mysqli_prepare($konekcija, "
    INSERT INTO rezervacije (korisnik_id, vozilo_id, datum_preuzimanja, datum_vracanja, mjesto_preuzimanja, napomena, ukupna_cijena, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'na cekanju')
");
mysqli_stmt_bind_param(
    $stmtUpis, 'iissssd',
    $_SESSION['korisnik_id'], $voziloId, $datumOd, $datumDo, $mjesto, $napomena, $ukupnaCijena
);

if (mysqli_stmt_execute($stmtUpis)) {
    $_SESSION['poruka_uspjeh'] = 'Rezervacija je uspješno poslata! Status možeš pratiti na stranici "Moje rezervacije".';
    preusmjeri('my-bookings.php');
} else {
    $_SESSION['poruka_greska'] = 'Došlo je do greške prilikom slanja rezervacije. Pokušaj ponovo.';
    preusmjeri('car-details.php?id=' . $voziloId);
}
