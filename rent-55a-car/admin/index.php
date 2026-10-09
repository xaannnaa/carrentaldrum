<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
zahtijevajAdmina();

$naslovStranice = 'Pregled';

$brojVozila = mysqli_fetch_assoc(mysqli_query($konekcija, "SELECT COUNT(*) AS n FROM vozila"))['n'];
$brojDostupnih = mysqli_fetch_assoc(mysqli_query($konekcija, "SELECT COUNT(*) AS n FROM vozila WHERE dostupno = 1"))['n'];
$brojNaCekanju = mysqli_fetch_assoc(mysqli_query($konekcija, "SELECT COUNT(*) AS n FROM rezervacije WHERE status = 'na cekanju'"))['n'];
$brojKorisnika = mysqli_fetch_assoc(mysqli_query($konekcija, "SELECT COUNT(*) AS n FROM korisnici WHERE uloga = 'korisnik'"))['n'];
$prihodPotvrdjeno = mysqli_fetch_assoc(mysqli_query($konekcija, "SELECT COALESCE(SUM(ukupna_cijena),0) AS s FROM rezervacije WHERE status IN ('potvrdjena','zavrsena')"))['s'];

$nedavneRezervacije = mysqli_query($konekcija, "
    SELECT r.*, v.naziv AS vozilo_naziv, k.ime, k.prezime
    FROM rezervacije r
    JOIN vozila v ON r.vozilo_id = v.id
    JOIN korisnici k ON r.korisnik_id = k.id
    ORDER BY r.datum_kreiranja DESC
    LIMIT 8
");

require_once 'includes/admin-header.php';
?>

<div class="admin-vrh">
    <h1>Pregled</h1>
</div>

<div class="stat-mreza">
    <div class="stat-kartica">
        <div class="broj"><?= $brojVozila ?></div>
        <div class="labela">Vozila ukupno</div>
    </div>
    <div class="stat-kartica">
        <div class="broj"><?= $brojDostupnih ?></div>
        <div class="labela">Trenutno dostupna</div>
    </div>
    <div class="stat-kartica">
        <div class="broj"><?= $brojNaCekanju ?></div>
        <div class="labela">Rezervacije na čekanju</div>
    </div>
    <div class="stat-kartica">
        <div class="broj"><?= $brojKorisnika ?></div>
        <div class="labela">Registrovani korisnici</div>
    </div>
    <div class="stat-kartica">
        <div class="broj"><?= formatCijena($prihodPotvrdjeno) ?></div>
        <div class="labela">Prihod (potvrđeno + završeno)</div>
    </div>
</div>

<h3 style="margin-bottom:16px">Posljednje rezervacije</h3>
<div class="tabela-wrap">
    <table>
        <thead>
        <tr>
            <th>Korisnik</th>
            <th>Vozilo</th>
            <th>Preuzimanje</th>
            <th>Vraćanje</th>
            <th>Ukupno</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($r = mysqli_fetch_assoc($nedavneRezervacije)): [$labela, $klasa] = statusInfo($r['status']); ?>
            <tr>
                <td><?= h($r['ime'] . ' ' . $r['prezime']) ?></td>
                <td><?= h($r['vozilo_naziv']) ?></td>
                <td><?= formatDatum($r['datum_preuzimanja']) ?></td>
                <td><?= formatDatum($r['datum_vracanja']) ?></td>
                <td><?= formatCijena($r['ukupna_cijena']) ?></td>
                <td><span class="bedz <?= $klasa ?>"><?= h($labela) ?></span></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
