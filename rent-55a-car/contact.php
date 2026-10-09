<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$naslovStranice = 'Kontakt';
$poslato = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ovdje bi se po potrebi slao email (npr. preko PHPMailer-a/SMTP-a).
    // Za sada samo potvrđujemo prijem poruke korisniku.
    $poslato = true;
}

require_once 'includes/header.php';
?>

<section style="padding-top:46px">
    <div class="kontejner">
        <div class="detalji-raspored" style="grid-template-columns: 1fr 1fr; align-items:flex-start">
            <div>
                <span class="eyebrow" style="display:block;margin-bottom:10px">Kontakt</span>
                <h1 style="margin-bottom:18px">Tu smo za sva pitanja</h1>
                <p style="color:var(--kamen);margin-bottom:30px">Javi nam se za rezervacije, izmjene termina ili bilo kakva pitanja u vezi sa vozilima.</p>

                <p style="margin-bottom:10px"><strong>Adresa:</strong> Nikšić, Crna Gora</p>
                <p style="margin-bottom:10px"><strong>Telefon:</strong> +382 67 000 000</p>
                <p style="margin-bottom:10px"><strong>Email:</strong> info@drum.me</p>
                <p><strong>Radno vrijeme:</strong> Pon–Ned, 08–20h</p>
            </div>

            <div class="karta-rezervacije">
                <?php if ($poslato): ?>
                    <div class="poruka poruka-uspjeh">Hvala! Tvoja poruka je primljena, javićemo se uskoro.</div>
                <?php endif; ?>
                <form method="post">
                    <div class="polje" style="margin-bottom:14px">
                        <label for="ime">Ime i prezime</label>
                        <input type="text" id="ime" name="ime" required>
                    </div>
                    <div class="polje" style="margin-bottom:14px">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="polje" style="margin-bottom:14px">
                        <label for="poruka">Poruka</label>
                        <textarea id="poruka" name="poruka" required></textarea>
                    </div>
                    <button type="submit" class="dugme dugme-primarno dugme-puno">Pošalji poruku</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
