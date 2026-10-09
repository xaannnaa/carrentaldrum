<?php
// Očekuje da je config/db.php i includes/functions.php već uključen prije ovoga
$trenutnaStranica = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($naslovStranice) ? h($naslovStranice) . ' | DRUM' : 'DRUM | Rent a Car Crna Gora' ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="sajt-header">
    <div class="kontejner sajt-header-unutra">
        <a href="index.php" class="logo">DRUM<span class="logo-tacka">.</span></a>

        <nav class="glavna-nav" id="glavnaNav">
            <a href="index.php" class="<?= $trenutnaStranica === 'index.php' ? 'aktivno' : '' ?>">Početna</a>
            <a href="cars.php" class="<?= $trenutnaStranica === 'cars.php' ? 'aktivno' : '' ?>">Vozila</a>
            <a href="contact.php" class="<?= $trenutnaStranica === 'contact.php' ? 'aktivno' : '' ?>">Kontakt</a>
            <?php if (jePrijavljen()): ?>
                <a href="my-bookings.php" class="<?= $trenutnaStranica === 'my-bookings.php' ? 'aktivno' : '' ?>">Moje rezervacije</a>
                <?php if (jeAdmin()): ?>
                    <a href="admin/index.php">Admin panel</a>
                <?php endif; ?>
                <a href="logout.php" class="dugme dugme-obris">Odjava</a>
            <?php else: ?>
                <a href="login.php" class="<?= $trenutnaStranica === 'login.php' ? 'aktivno' : '' ?>">Prijava</a>
                <a href="register.php" class="dugme dugme-malo">Registracija</a>
            <?php endif; ?>
        </nav>

        <button class="hamburger" id="hamburgerDugme" aria-label="Otvori meni">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main>
