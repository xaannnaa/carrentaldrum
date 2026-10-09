<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($konekcija, "SELECT v.*, k.naziv AS kategorija_naziv FROM vozila v LEFT JOIN kategorije k ON v.kategorija_id = k.id WHERE v.id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$vozilo = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$vozilo) {
    preusmjeri('cars.php');
}

$naslovStranice = $vozilo['naziv'];
$datumOd = $_GET['datum_od'] ?? '';
$datumDo = $_GET['datum_do'] ?? '';

require_once 'includes/header.php';
?>

<section style="padding-top:40px">
    <div class="kontejner">
        <?php prikaziPoruke(); ?>

        <div class="detalji-raspored">
            <div>
                <div class="detalji-slika">
                    <img src="uploads/cars/<?= h($vozilo['slika']) ?>" alt="<?= h($vozilo['naziv']) ?>"
                         onerror="this.src='https://placehold.co/800x500/1B1B1D/F3F1EC?text=DRUM'">
                </div>
            </div>

            <div>
                <div class="detalji-info">
                    <span class="kartica-kategorija" style="position:static;display:inline-block;margin-bottom:14px"><?= h($vozilo['kategorija_naziv'] ?? 'Vozilo') ?></span>
                    <h1><?= h($vozilo['naziv']) ?></h1>
                    <div class="specifikacije">
                        <span>📅 <?= h($vozilo['godiste']) ?></span>
                        <span>⚙️ <?= h($vozilo['mjenjac']) ?></span>
                        <span>⛽ <?= h($vozilo['gorivo']) ?></span>
                        <span>👤 <?= h($vozilo['broj_sjedista']) ?> sjedišta</span>
                    </div>
                    <p class="detalji-opis"><?= nl2br(h($vozilo['opis'])) ?></p>
                </div>

                <div class="karta-rezervacije">
                    <div class="cijena-veca"><?= formatCijena($vozilo['cijena_po_danu']) ?> <span>/ dan</span></div>
                    <hr class="razdvaja">

                    <?php if (!jePrijavljen()): ?>
                        <p style="margin-bottom:16px;color:var(--kamen)">Prijavi se da bi rezervisao/la ovo vozilo.</p>
                        <a href="login.php" class="dugme dugme-primarno dugme-puno">Prijavi se za rezervaciju</a>
                    <?php elseif (!$vozilo['dostupno']): ?>
                        <p style="color:var(--crvena);font-weight:600">Ovo vozilo trenutno nije dostupno.</p>
                    <?php else: ?>
                        <form action="booking.php" method="post">
                            <input type="hidden" name="vozilo_id" value="<?= $vozilo['id'] ?>">
                            <div class="polje" style="margin-bottom:14px">
                                <label for="datum_preuzimanja">Datum preuzimanja</label>
                                <input type="date" id="datum_preuzimanja" name="datum_preuzimanja" value="<?= h($datumOd) ?>" required>
                            </div>
                            <div class="polje" style="margin-bottom:14px">
                                <label for="datum_vracanja">Datum vraćanja</label>
                                <input type="date" id="datum_vracanja" name="datum_vracanja" value="<?= h($datumDo) ?>" required>
                            </div>
                            <div class="polje" style="margin-bottom:14px">
                                <label for="mjesto_preuzimanja">Mjesto preuzimanja</label>
                                <input type="text" id="mjesto_preuzimanja" name="mjesto_preuzimanja" placeholder="npr. Aerodrom Podgorica" required>
                            </div>
                            <div class="polje" style="margin-bottom:14px">
                                <label for="napomena">Napomena (opciono)</label>
                                <textarea id="napomena" name="napomena" placeholder="Dodatne želje ili pitanja..."></textarea>
                            </div>

                            <hr class="razdvaja">
                            <div class="ukupno-red">
                                <span>Broj dana: <strong id="prikazBrojDana">–</strong></span>
                                <span id="prikazUkupnaCijena" data-cijena-po-danu="<?= h($vozilo['cijena_po_danu']) ?>">0,00 €</span>
                            </div>

                            <button type="submit" id="dugmeRezervisi" class="dugme dugme-primarno dugme-puno" style="margin-top:18px" disabled>Pošalji rezervaciju</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
