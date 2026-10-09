<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
zahtijevajAdmina();

$naslovStranice = 'Vozila';

$vozila = mysqli_query($konekcija, "
    SELECT v.*, k.naziv AS kategorija_naziv
    FROM vozila v LEFT JOIN kategorije k ON v.kategorija_id = k.id
    ORDER BY v.id DESC
");

require_once 'includes/admin-header.php';
?>

<div class="admin-toolbar">
    <h1 style="font-size:1.6rem">Vozila</h1>
    <a href="car-edit.php" class="dugme dugme-primarno">+ Dodaj vozilo</a>
</div>

<div class="tabela-wrap">
    <table>
        <thead>
        <tr>
            <th>Slika</th>
            <th>Naziv</th>
            <th>Kategorija</th>
            <th>Cijena/dan</th>
            <th>Dostupnost</th>
            <th>Akcije</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($v = mysqli_fetch_assoc($vozila)): ?>
            <tr>
                <td><img class="thumb-tabela" src="../uploads/cars/<?= h($v['slika']) ?>" onerror="this.src='https://placehold.co/100x70/1B1B1D/F3F1EC?text=DRUM'" alt=""></td>
                <td><?= h($v['naziv']) ?></td>
                <td><?= h($v['kategorija_naziv'] ?? '–') ?></td>
                <td><?= formatCijena($v['cijena_po_danu']) ?></td>
                <td>
                    <?php if ($v['dostupno']): ?>
                        <span class="bedz bedz-zeleni">Dostupno</span>
                    <?php else: ?>
                        <span class="bedz bedz-crveni">Nedostupno</span>
                    <?php endif; ?>
                </td>
                <td class="akcije-celija">
                    <a href="car-edit.php?id=<?= $v['id'] ?>" class="dugme dugme-obris">Izmijeni</a>
                    <a href="car-delete.php?id=<?= $v['id'] ?>" class="dugme dugme-opasnost zahtijeva-potvrdu" data-potvrda="Obrisati vozilo <?= h(addslashes($v['naziv'])) ?>? Ova akcija je nepovratna.">Obriši</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
