<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
zahtijevajAdmina();

$naslovStranice = 'Korisnici';

$korisnici = mysqli_query($konekcija, "
    SELECT k.*, COUNT(r.id) AS broj_rezervacija
    FROM korisnici k
    LEFT JOIN rezervacije r ON r.korisnik_id = k.id
    GROUP BY k.id
    ORDER BY k.datum_registracije DESC
");

require_once 'includes/admin-header.php';
?>

<div class="admin-vrh">
    <h1 style="font-size:1.6rem">Korisnici</h1>
</div>

<div class="tabela-wrap">
    <table>
        <thead>
        <tr>
            <th>Ime i prezime</th>
            <th>Email</th>
            <th>Telefon</th>
            <th>Uloga</th>
            <th>Broj rezervacija</th>
            <th>Registrovan</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($k = mysqli_fetch_assoc($korisnici)): ?>
            <tr>
                <td><?= h($k['ime'] . ' ' . $k['prezime']) ?></td>
                <td><?= h($k['email']) ?></td>
                <td><?= h($k['telefon'] ?: '–') ?></td>
                <td><span class="bedz <?= $k['uloga'] === 'admin' ? 'bedz-zeleni' : 'bedz-sivi' ?>"><?= $k['uloga'] === 'admin' ? 'Admin' : 'Korisnik' ?></span></td>
                <td><?= $k['broj_rezervacija'] ?></td>
                <td><?= date('d.m.Y.', strtotime($k['datum_registracije'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
