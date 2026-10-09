<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$naslovStranice = 'Registracija';

if (jePrijavljen()) {
    preusmjeri('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $lozinka = $_POST['lozinka'] ?? '';
    $lozinkaPotvrda = $_POST['lozinka_potvrda'] ?? '';

    $greske = [];
    if ($ime === '' || $prezime === '') $greske[] = 'Unesi ime i prezime.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $greske[] = 'Email adresa nije ispravna.';
    if (strlen($lozinka) < 6) $greske[] = 'Lozinka mora imati najmanje 6 karaktera.';
    if ($lozinka !== $lozinkaPotvrda) $greske[] = 'Lozinke se ne podudaraju.';

    if (!$greske) {
        $stmt = mysqli_prepare($konekcija, "SELECT id FROM korisnici WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->fetch_assoc()) {
            $greske[] = 'Nalog sa ovim emailom već postoji.';
        }
    }

    if (!$greske) {
        $hash = password_hash($lozinka, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($konekcija, "INSERT INTO korisnici (ime, prezime, email, lozinka, telefon, uloga) VALUES (?, ?, ?, ?, ?, 'korisnik')");
        mysqli_stmt_bind_param($stmt, 'sssss', $ime, $prezime, $email, $hash, $telefon);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['korisnik_id'] = mysqli_insert_id($konekcija);
            $_SESSION['ime'] = $ime;
            $_SESSION['uloga'] = 'korisnik';
            $_SESSION['poruka_uspjeh'] = 'Dobrodošao/la, ' . $ime . '! Nalog je uspješno kreiran.';
            preusmjeri('index.php');
        } else {
            $greske[] = 'Došlo je do greške. Pokušaj ponovo.';
        }
    }

    if ($greske) {
        $_SESSION['poruka_greska'] = implode(' ', $greske);
    }
}

require_once 'includes/header.php';
?>

<div class="forma-karta">
    <h1>Registracija</h1>
    <p class="forma-podnaslov">Napravi nalog za rezervaciju vozila.</p>

    <?php prikaziPoruke(); ?>

    <form method="post">
        <div class="polje">
            <label for="ime">Ime</label>
            <input type="text" id="ime" name="ime" value="<?= h($_POST['ime'] ?? '') ?>" required>
        </div>
        <div class="polje">
            <label for="prezime">Prezime</label>
            <input type="text" id="prezime" name="prezime" value="<?= h($_POST['prezime'] ?? '') ?>" required>
        </div>
        <div class="polje">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="polje">
            <label for="telefon">Telefon (opciono)</label>
            <input type="text" id="telefon" name="telefon" value="<?= h($_POST['telefon'] ?? '') ?>">
        </div>
        <div class="polje">
            <label for="lozinka">Lozinka</label>
            <input type="password" id="lozinka" name="lozinka" minlength="6" required>
        </div>
        <div class="polje">
            <label for="lozinka_potvrda">Potvrdi lozinku</label>
            <input type="password" id="lozinka_potvrda" name="lozinka_potvrda" minlength="6" required>
        </div>
        <button type="submit" class="dugme dugme-primarno dugme-puno">Registruj se</button>
    </form>

    <p class="forma-footer-link">Već imaš nalog? <a href="login.php">Prijavi se</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>
