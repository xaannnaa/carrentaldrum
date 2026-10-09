<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

zahtijevajPrijavu();
$naslovStranice = 'Moje rezervacije';

$stmt = mysqli_prepare($konekcija, "
    SELECT r.*, v.naziv AS vozilo_naziv, v.slika
    FROM rezervacije r
    JOIN vozila v ON r.vozilo_id = v.id
    WHERE r.korisnik_id = ?
    ORDER BY r.datum_kreiranja DESC
");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['korisnik_id']);
mysqli_stmt_execute($stmt);
$rezervacije = mysqli_stmt_get_result($stmt);

require_once 'includes/header.php';
?>

<section style="padding-top:46px">
    <div class="kontejner">
        <div class="sekcija-naslov" style="margin-bottom:30px;text-align:left;max-width:none">
            <span class="eyebrow">Nalog</span>
            <h2>Moje rezervacije</h2>
        </div>

        <?php prikaziPoruke(); ?>

        <?php if (mysqli_num_rows($rezervacije) === 0): ?>
            <div class="prazno-stanje">
                Još uvijek nemaš nijednu rezervaciju.
                <br><br>
                <a href="cars.php" class="dugme dugme-primarno">Pregledaj vozila</a>
            </div>
        <?php else: ?>
            <div class="tabela-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Vozilo</th>
                        <th>Preuzimanje</th>
                        <th>Vraćanje</th>
                        <th>Mjesto</th>
                        <th>Ukupno</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($r = mysqli_fetch_assoc($rezervacije)): [$labela, $klasa] = statusInfo($r['status']); ?>
                        <tr>
                            <td><?= h($r['vozilo_naziv']) ?></td>
                            <td><?= formatDatum($r['datum_preuzimanja']) ?></td>
                            <td><?= formatDatum($r['datum_vracanja']) ?></td>
                            <td><?= h($r['mjesto_preuzimanja']) ?></td>
                            <td><?= formatCijena($r['ukupna_cijena']) ?></td>
                            <td><span class="bedz <?= $klasa ?>"><?= h($labela) ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
