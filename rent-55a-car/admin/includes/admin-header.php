<?php
// Očekuje da je config/db.php, includes/functions.php uključen i zahtijevajAdmina() pozvan prije ovoga
$trenutnaAdminStranica = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($naslovStranice) ? h($naslovStranice) . ' | Admin' : 'Admin panel' ?> | DRUM</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<div class="admin-raspored">
    <aside class="admin-sidebar">
        <a href="index.php" class="logo">DRUM<span class="logo-tacka">.</span></a>
        <nav>
            <a href="index.php" class="<?= $trenutnaAdminStranica === 'index.php' ? 'aktivno' : '' ?>">Pregled</a>
            <a href="cars.php" class="<?= in_array($trenutnaAdminStranica, ['cars.php','car-edit.php']) ? 'aktivno' : '' ?>">Vozila</a>
            <a href="bookings.php" class="<?= $trenutnaAdminStranica === 'bookings.php' ? 'aktivno' : '' ?>">Rezervacije</a>
            <a href="users.php" class="<?= $trenutnaAdminStranica === 'users.php' ? 'aktivno' : '' ?>">Korisnici</a>
            <div class="razdvoji"></div>
            <a href="../index.php">&larr; Nazad na sajt</a>
            <a href="logout.php">Odjava</a>
        </nav>
    </aside>

    <div class="admin-sadrzaj">
        <?php prikaziPoruke(); ?>
