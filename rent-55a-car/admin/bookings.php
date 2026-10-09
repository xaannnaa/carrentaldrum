<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
zahtijevajAdmina();

$naslovStranice = 'Rezervacije';

// ---- Promjena statusa rezervacije ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rezervacija_id'], $_POST['status'])) {
    $rezId = (int)$_POST['rezervacija_id'];
    $noviStatus = $_POST['status'];
    $dozvoljeniStatusi = ['na cekanju','potvrdjena','odbijena','zavrsena','otkazana'];

    if (in_array($noviStatus, $dozvoljeniStatusi)) {
        $stmt = mysqli_prepare($konekcija, "UPDATE rezervacije SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $noviStatus, $rezId);
        mysqli_stmt_execute($stmt);
        $_SESSION['poruka_uspjeh'] = 'Status rezervacije je ažuriran.';
    }
    preusmjeri('bookings.php');
}

$filterStatus = $_GET['status'] ?? '';
$upit = "
    SELECT r.*, v.naziv AS vozilo_naziv, k.ime, k.prezime, k.email
    FROM rezervacije r
    JOIN vozila v ON r.vozilo_id = v.id
    JOIN korisnici k ON r.korisnik_id = k.id
";
if ($filterStatus !== '') {
    $upit .= " WHERE r.status = ? ORDER BY r.datum_kreiranja DESC";
    $stmt = mysqli_prepare($konekcija, $upit);
    mysqli_stmt_bind_param($stmt, 's', $filterStatus);
    mysqli_stmt_execute($stmt);
    $rezervacije = mysqli_stmt_get_result($stmt);
} else {
    $upit .= " ORDER BY r.datum_kreiranja DESC";
    $rezervacije = mysqli_query($konekcija, $upit);
}

require_once 'includes/admin-header.php';
?>

<div class="admin-toolbar">
    <h1 style="font-size:1.6rem">Rezervacije</h1>
    <form method="get">
        <select name="status" class="status-select" onchange="this.form.submit()">
            <option value="">Svi statusi</option>
            <?php foreach (['na cekanju'=>'Na čekanju','potvrdjena'=>'Potvrđena','odbijena'=>'Odbijena','zavrsena'=>'Završena','otkazana'=>'Otkazana'] as $vr => $lb): ?>
                <option value="<?= $vr ?>" <?= $filterStatus === $vr ? 'selected' : '' ?>><?= $lb ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="tabela-wrap">
    <table>
        <thead>
        <tr>
            <th>Korisnik</th>
            <th>Vozilo</th>
            <th>Preuzimanje</th>
            <th>Vraćanje</th>
            <th>Mjesto</th>
            <th>Ukupno</th>
            <th>Status</th>
            <th>Promijeni</th>
        </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($rezervacije) === 0): ?>
            <tr><td colspan="8" style="text-align:center;color:var(--kamen);padding:30px">Nema rezervacija za ovaj filter.</td></tr>
        <?php endif; ?>
        <?php while ($r = mysqli_fetch_assoc($rezervacije)): [$labela, $klasa] = statusInfo($r['status']); ?>
            <tr>
                <td><?= h($r['ime'] . ' ' . $r['prezime']) ?><br><small style="color:var(--kamen)"><?= h($r['email']) ?></small></td>
                <td><?= h($r['vozilo_naziv']) ?></td>
                <td><?= formatDatum($r['datum_preuzimanja']) ?></td>
                <td><?= formatDatum($r['datum_vracanja']) ?></td>
                <td><?= h($r['mjesto_preuzimanja']) ?></td>
                <td><?= formatCijena($r['ukupna_cijena']) ?></td>
                <td><span class="bedz <?= $klasa ?>"><?= h($labela) ?></span></td>
                <td>
                    <form method="post" style="display:flex;gap:6px">
                        <input type="hidden" name="rezervacija_id" value="<?= $r['id'] ?>">
                        <select name="status" class="status-select">
                            <?php foreach (['na cekanju'=>'Na čekanju','potvrdjena'=>'Potvrđena','odbijena'=>'Odbijena','zavrsena'=>'Završena','otkazana'=>'Otkazana'] as $vr => $lb): ?>
                                <option value="<?= $vr ?>" <?= $r['status'] === $vr ? 'selected' : '' ?>><?= $lb ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="dugme dugme-malo">Sačuvaj</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
