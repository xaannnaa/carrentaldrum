<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
zahtijevajAdmina();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($konekcija, "DELETE FROM vozila WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['poruka_uspjeh'] = 'Vozilo je obrisano.';
    } else {
        $_SESSION['poruka_greska'] = 'Greška prilikom brisanja vozila.';
    }
}

preusmjeri('cars.php');
