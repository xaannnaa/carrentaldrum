<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$naslovStranice = 'Prijava';

if (jePrijavljen()) {
    preusmjeri('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $lozinka = $_POST['lozinka'] ?? '';

    $stmt = mysqli_prepare($konekcija, "SELECT * FROM korisnici WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $korisnik = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if ($korisnik && password_verify($lozinka, $korisnik['lozinka'])) {
        $_SESSION['korisnik_id'] = $korisnik['id'];
        $_SESSION['ime'] = $korisnik['ime'];
        $_SESSION['uloga'] = $korisnik['uloga'];

        if ($korisnik['uloga'] === 'admin') {
            preusmjeri('admin/index.php');
        }
        preusmjeri('index.php');
    } else {
        $_SESSION['poruka_greska'] = 'Pogrešan email ili lozinka.';
    }
}

require_once 'includes/header.php';
?>

<div class="forma-karta">
    <h1>Prijava</h1>
    <p class="forma-podnaslov">Prijavi se da bi rezervisao/la vozilo.</p>

    <?php prikaziPoruke(); ?>

    <form method="post">
        <div class="polje">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <div class="polje">
            <label for="lozinka">Lozinka</label>
            <input type="password" id="lozinka" name="lozinka" required>
        </div>
        <button type="submit" class="dugme dugme-primarno dugme-puno">Prijavi se</button>
    </form>

    <p class="forma-footer-link">Nemaš nalog? <a href="register.php">Registruj se</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>
