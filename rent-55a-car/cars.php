<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$naslovStranice = 'Vozila';

// ---- Učitaj filtere iz GET parametara ----
$kategorijaId = $_GET['kategorija'] ?? '';
$datumOd = $_GET['datum_od'] ?? '';
$datumDo = $_GET['datum_do'] ?? '';
$sortiraj = $_GET['sort'] ?? '';

$uslovi = ["v.dostupno = 1"];
$parametri = [];
$tipovi = '';

if ($kategorijaId !== '') {
    $uslovi[] = "v.kategorija_id = ?";
    $parametri[] = $kategorijaId;
    $tipovi .= 'i';
}

// Isključi vozila koja su već rezervisana (potvrđena ili na čekanju) u traženom periodu
if ($datumOd !== '' && $datumDo !== '') {
    $uslovi[] = "v.id NOT IN (
        SELECT vozilo_id FROM rezervacije
        WHERE status IN ('na cekanju','potvrdjena')
        AND NOT (datum_vracanja <= ? OR datum_preuzimanja >= ?)
    )";
    $parametri[] = $datumOd;
    $parametri[] = $datumDo;
    $tipovi .= 'ss';
}

$redoslijed = "v.id DESC";
if ($sortiraj === 'cijena_rastuce') $redoslijed = "v.cijena_po_danu ASC";
if ($sortiraj === 'cijena_opadajuce') $redoslijed = "v.cijena_po_danu DESC";

$upit = "SELECT v.*, k.naziv AS kategorija_naziv
         FROM vozila v
         LEFT JOIN kategorije k ON v.kategorija_id = k.id
         WHERE " . implode(' AND ', $uslovi) . "
         ORDER BY $redoslijed";

$stmt = mysqli_prepare($konekcija, $upit);
if ($parametri) {
    mysqli_stmt_bind_param($stmt, $tipovi, ...$parametri);
}
mysqli_stmt_execute($stmt);
$rezultat = mysqli_stmt_get_result($stmt);

$kategorije = mysqli_query($konekcija, "SELECT * FROM kategorije ORDER BY naziv");

require_once 'includes/header.php';
?>

<section style="padding-top:46px;padding-bottom:0;background:var(--bijela)">
    <div class="kontejner">
        <div class="sekcija-naslov" style="margin-bottom:30px">
            <span class="eyebrow">Ponuda</span>
            <h2>Sva vozila</h2>
        </div>
    </div>
</section>

<section style="padding-top:30px">
    <div class="kontejner">
        <form class="filter-traka" method="get">
            <div class="polje">
                <label for="kategorija">Kategorija</label>
                <select name="kategorija" id="kategorija">
                    <option value="">Sve kategorije</option>
                    <?php mysqli_data_seek($kategorije, 0); while ($k = mysqli_fetch_assoc($kategorije)): ?>
                        <option value="<?= $k['id'] ?>" <?= ($kategorijaId == $k['id']) ? 'selected' : '' ?>><?= h($k['naziv']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="polje">
                <label for="datum_od">Preuzimanje</label>
                <input type="date" name="datum_od" id="datum_od" value="<?= h($datumOd) ?>">
            </div>
            <div class="polje">
                <label for="datum_do">Vraćanje</label>
                <input type="date" name="datum_do" id="datum_do" value="<?= h($datumDo) ?>">
            </div>
            <div class="polje">
                <label for="sort">Sortiraj</label>
                <select name="sort" id="sort">
                    <option value="">Najnovije</option>
                    <option value="cijena_rastuce" <?= $sortiraj === 'cijena_rastuce' ? 'selected' : '' ?>>Cijena: niža ka višoj</option>
                    <option value="cijena_opadajuce" <?= $sortiraj === 'cijena_opadajuce' ? 'selected' : '' ?>>Cijena: viša ka nižoj</option>
                </select>
            </div>
            <button type="submit" class="dugme dugme-primarno">Filtriraj</button>
        </form>

        <div class="mreza-vozila">
            <?php if (mysqli_num_rows($rezultat) === 0): ?>
                <div class="prazno-stanje">Nema vozila koja odgovaraju traženim kriterijumima. Pokušaj sa drugim datumima ili kategorijom.</div>
            <?php else: while ($vozilo = mysqli_fetch_assoc($rezultat)): ?>
                <div class="kartica-vozila">
                    <div class="kartica-vozila-slika">
                        <span class="kartica-kategorija"><?= h($vozilo['kategorija_naziv'] ?? 'Vozilo') ?></span>
                        <img src="uploads/cars/<?= h($vozilo['slika']) ?>" alt="<?= h($vozilo['naziv']) ?>"
                             onerror="this.src='https://placehold.co/600x375/1B1B1D/F3F1EC?text=DRUM'">
                    </div>
                    <div class="kartica-vozila-tijelo">
                        <h3><?= h($vozilo['naziv']) ?></h3>
                        <div class="specifikacije">
                            <span><?= h($vozilo['godiste']) ?></span>
                            <span><?= h($vozilo['mjenjac']) ?></span>
                            <span><?= h($vozilo['gorivo']) ?></span>
                            <span><?= h($vozilo['broj_sjedista']) ?> sjedišta</span>
                        </div>
                        <div class="kartica-vozila-dno">
                            <span class="cijena-tablica"><?= formatCijena($vozilo['cijena_po_danu']) ?><small>/dan</small></span>
                            <a href="car-details.php?id=<?= $vozilo['id'] ?><?= $datumOd ? '&datum_od=' . urlencode($datumOd) . '&datum_do=' . urlencode($datumDo) : '' ?>" class="dugme dugme-malo">Detalji</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
