<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
zahtijevajAdmina();

$id = (int)($_GET['id'] ?? 0);
$vozilo = ['naziv'=>'','kategorija_id'=>'','godiste'=>date('Y'),'gorivo'=>'Dizel','mjenjac'=>'Manuelni','broj_sjedista'=>5,'cijena_po_danu'=>'','slika'=>'no-image.jpg','opis'=>'','dostupno'=>1];
$izmjena = false;

if ($id > 0) {
    $stmt = mysqli_prepare($konekcija, "SELECT * FROM vozila WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $postojece = mysqli_stmt_get_result($stmt)->fetch_assoc();
    if ($postojece) {
        $vozilo = $postojece;
        $izmjena = true;
    }
}

$naslovStranice = $izmjena ? 'Izmijeni vozilo' : 'Dodaj vozilo';
$greske = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naziv = trim($_POST['naziv'] ?? '');
    $kategorijaId = (int)($_POST['kategorija_id'] ?? 0) ?: null;
    $godiste = (int)($_POST['godiste'] ?? 0);
    $gorivo = $_POST['gorivo'] ?? 'Dizel';
    $mjenjac = $_POST['mjenjac'] ?? 'Manuelni';
    $brojSjedista = (int)($_POST['broj_sjedista'] ?? 5);
    $cijenaPoDanu = (float)str_replace(',', '.', $_POST['cijena_po_danu'] ?? '0');
    $opis = trim($_POST['opis'] ?? '');
    $dostupno = isset($_POST['dostupno']) ? 1 : 0;
    $slika = $vozilo['slika'];

    if ($naziv === '') $greske[] = 'Unesi naziv vozila.';
    if ($cijenaPoDanu <= 0) $greske[] = 'Cijena po danu mora biti veća od 0.';

    // ---- Upload slike (opciono) ----
    if (!empty($_FILES['slika']['name'])) {
        $dozvoljeneEkstenzije = ['jpg','jpeg','png','webp'];
        $ekstenzija = strtolower(pathinfo($_FILES['slika']['name'], PATHINFO_EXTENSION));

        if (!in_array($ekstenzija, $dozvoljeneEkstenzije)) {
            $greske[] = 'Slika mora biti u formatu JPG, PNG ili WEBP.';
        } elseif ($_FILES['slika']['size'] > 4 * 1024 * 1024) {
            $greske[] = 'Slika ne smije biti veća od 4MB.';
        } else {
            $noviNaziv = 'vozilo_' . uniqid() . '.' . $ekstenzija;
            $putanja = '../uploads/cars/' . $noviNaziv;
            if (move_uploaded_file($_FILES['slika']['tmp_name'], $putanja)) {
                $slika = $noviNaziv;
            } else {
                $greske[] = 'Greška prilikom otpremanja slike.';
            }
        }
    }

    if (!$greske) {
        if ($izmjena) {
            $stmt = mysqli_prepare($konekcija, "
                UPDATE vozila SET naziv=?, kategorija_id=?, godiste=?, gorivo=?, mjenjac=?, broj_sjedista=?, cijena_po_danu=?, slika=?, opis=?, dostupno=?
                WHERE id=?
            ");
            // s(naziv) i(kategorija) i(godiste) s(gorivo) s(mjenjac) i(sjedista) d(cijena) s(slika) s(opis) i(dostupno) i(id)
            mysqli_stmt_bind_param($stmt, 'siisidssii', $naziv, $kategorijaId, $godiste, $gorivo, $mjenjac, $brojSjedista, $cijenaPoDanu, $slika, $opis, $dostupno, $id);
            $uspjeh = mysqli_stmt_execute($stmt);
            $porukaUspjeha = 'Vozilo je uspješno izmijenjeno.';
        } else {
            $stmt = mysqli_prepare($konekcija, "
                INSERT INTO vozila (naziv, kategorija_id, godiste, gorivo, mjenjac, broj_sjedista, cijena_po_danu, slika, opis, dostupno)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param($stmt, 'siisidssi', $naziv, $kategorijaId, $godiste, $gorivo, $mjenjac, $brojSjedista, $cijenaPoDanu, $slika, $opis, $dostupno);
            $uspjeh = mysqli_stmt_execute($stmt);
            $porukaUspjeha = 'Vozilo je uspješno dodato.';
        }

        if ($uspjeh) {
            $_SESSION['poruka_uspjeh'] = $porukaUspjeha;
            preusmjeri('cars.php');
        } else {
            $greske[] = 'Greška prilikom čuvanja: ' . mysqli_error($konekcija);
        }
    }

    // Zadrži unesene vrijednosti u formi ako ima grešaka
    $vozilo = array_merge($vozilo, $_POST, ['slika' => $slika]);
}

$kategorije = mysqli_query($konekcija, "SELECT * FROM kategorije ORDER BY naziv");

require_once 'includes/admin-header.php';
?>

<div class="admin-vrh">
    <h1><?= $izmjena ? 'Izmijeni vozilo' : 'Dodaj novo vozilo' ?></h1>
</div>

<?php if ($greske): ?>
    <div class="poruka poruka-greska"><?= implode('<br>', array_map('h', $greske)) ?></div>
<?php endif; ?>

<form class="forma-admin" method="post" enctype="multipart/form-data">
    <div class="polje">
        <label for="naziv">Naziv vozila</label>
        <input type="text" id="naziv" name="naziv" value="<?= h($vozilo['naziv']) ?>" required>
    </div>

    <div class="red-dva">
        <div class="polje">
            <label for="kategorija_id">Kategorija</label>
            <select id="kategorija_id" name="kategorija_id">
                <option value="">Bez kategorije</option>
                <?php mysqli_data_seek($kategorije, 0); while ($k = mysqli_fetch_assoc($kategorije)): ?>
                    <option value="<?= $k['id'] ?>" <?= $vozilo['kategorija_id'] == $k['id'] ? 'selected' : '' ?>><?= h($k['naziv']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="polje">
            <label for="godiste">Godište</label>
            <input type="number" id="godiste" name="godiste" min="1990" max="<?= date('Y') + 1 ?>" value="<?= h($vozilo['godiste']) ?>" required>
        </div>
    </div>

    <div class="red-dva">
        <div class="polje">
            <label for="gorivo">Gorivo</label>
            <select id="gorivo" name="gorivo">
                <?php foreach (['Benzin','Dizel','Hibrid','Elektricni'] as $g): ?>
                    <option value="<?= $g ?>" <?= $vozilo['gorivo'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="polje">
            <label for="mjenjac">Mjenjač</label>
            <select id="mjenjac" name="mjenjac">
                <?php foreach (['Manuelni','Automatski'] as $m): ?>
                    <option value="<?= $m ?>" <?= $vozilo['mjenjac'] === $m ? 'selected' : '' ?>><?= $m ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="red-dva">
        <div class="polje">
            <label for="broj_sjedista">Broj sjedišta</label>
            <input type="number" id="broj_sjedista" name="broj_sjedista" min="1" max="9" value="<?= h($vozilo['broj_sjedista']) ?>" required>
        </div>
        <div class="polje">
            <label for="cijena_po_danu">Cijena po danu (€)</label>
            <input type="text" id="cijena_po_danu" name="cijena_po_danu" value="<?= h($vozilo['cijena_po_danu']) ?>" required>
        </div>
    </div>

    <div class="polje">
        <label for="opis">Opis</label>
        <textarea id="opis" name="opis"><?= h($vozilo['opis']) ?></textarea>
    </div>

    <div class="polje">
        <label for="slika">Slika vozila <?= $izmjena ? '(ostavi prazno da zadržiš trenutnu)' : '' ?></label>
        <input type="file" id="slika" name="slika" accept=".jpg,.jpeg,.png,.webp">
        <?php if ($izmjena && $vozilo['slika'] !== 'no-image.jpg'): ?>
            <img class="thumb-tabela" style="width:120px;height:80px;margin-top:8px" src="../uploads/cars/<?= h($vozilo['slika']) ?>" alt="">
        <?php endif; ?>
    </div>

    <div class="polje" style="display:flex;align-items:center;gap:10px;flex-direction:row">
        <input type="checkbox" id="dostupno" name="dostupno" style="width:auto" <?= $vozilo['dostupno'] ? 'checked' : '' ?>>
        <label for="dostupno" style="margin:0;text-transform:none;font-size:0.95rem">Vozilo je dostupno za rezervaciju</label>
    </div>

    <button type="submit" class="dugme dugme-primarno"><?= $izmjena ? 'Sačuvaj izmjene' : 'Dodaj vozilo' ?></button>
</form>

<?php require_once 'includes/admin-footer.php'; ?>
