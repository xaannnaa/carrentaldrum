<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$naslovStranice = 'Početna';

// Učitaj do 6 dostupnih vozila za izdvojenu ponudu
$upit = "SELECT v.*, k.naziv AS kategorija_naziv
         FROM vozila v
         LEFT JOIN kategorije k ON v.kategorija_id = k.id
         WHERE v.dostupno = 1
         ORDER BY v.id DESC
         LIMIT 6";
$rezultat = mysqli_query($konekcija, $upit);

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="kontejner">
        <div class="hero-sadrzaj">
            <span class="hero-eyebrow">Rent a car &middot; Nikšić, Crna Gora</span>
            <h1>Vozi svojim putem.</h1>
            <p class="lid">Od gradskih ulica do planinskih serpentina — iznajmi pouzdano vozilo za onoliko dana koliko ti treba, bez skrivenih troškova.</p>
            <div class="hero-dugmad">
                <a href="cars.php" class="dugme dugme-primarno">Pogledaj vozila</a>
                <a href="contact.php" class="dugme dugme-sekundarno">Kontaktiraj nas</a>
            </div>
        </div>

        <form class="brza-pretraga" action="cars.php" method="get">
            <div class="polje">
                <label for="datum_od">Preuzimanje</label>
                <input type="date" id="datum_od" name="datum_od">
            </div>
            <div class="polje">
                <label for="datum_do">Vraćanje</label>
                <input type="date" id="datum_do" name="datum_do">
            </div>
            <div class="polje">
                <label for="kategorija">Kategorija</label>
                <select id="kategorija" name="kategorija">
                    <option value="">Sve kategorije</option>
                    <?php
                    $kat = mysqli_query($konekcija, "SELECT * FROM kategorije ORDER BY naziv");
                    while ($k = mysqli_fetch_assoc($kat)) {
                        echo '<option value="' . $k['id'] . '">' . h($k['naziv']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="dugme dugme-primarno">Pretraži</button>
        </form>
    </div>
</section>

<section>
    <div class="kontejner">
        <div class="sekcija-naslov">
            <span class="eyebrow">Ponuda</span>
            <h2>Izdvojena vozila</h2>
            <p>Spremna za preuzimanje već danas.</p>
        </div>

        <div class="mreza-vozila">
            <?php if (mysqli_num_rows($rezultat) === 0): ?>
                <div class="prazno-stanje">Trenutno nema dostupnih vozila. Pokušaj kasnije.</div>
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
                            <a href="car-details.php?id=<?= $vozilo['id'] ?>" class="dugme dugme-malo">Detalji</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>

<section style="background:var(--bijela)">
    <div class="kontejner">
        <div class="sekcija-naslov">
            <span class="eyebrow">Proces</span>
            <h2>Kako funkcioniše</h2>
        </div>
        <div class="koraci">
            <div class="korak">
                <div class="korak-oznaka">01</div>
                <h3>Izaberi vozilo</h3>
                <p>Pretraži ponudu po kategoriji i datumima koji ti odgovaraju.</p>
            </div>
            <div class="korak">
                <div class="korak-oznaka">02</div>
                <h3>Pošalji rezervaciju</h3>
                <p>Popuni datume i mjesto preuzimanja — rezervacija ide na potvrdu.</p>
            </div>
            <div class="korak">
                <div class="korak-oznaka">03</div>
                <h3>Potvrda</h3>
                <p>Naš tim potvrđuje rezervaciju i javlja se za detalje preuzimanja.</p>
            </div>
            <div class="korak">
                <div class="korak-oznaka">04</div>
                <h3>Preuzmi i vozi</h3>
                <p>Preuzmi vozilo na dogovorenoj lokaciji i kreni na put.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
