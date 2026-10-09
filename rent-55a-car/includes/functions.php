<?php
/**
 * Pomoćne (helper) funkcije
 */

// Bezbjedan ispis teksta (zaštita od XSS-a)
function h($tekst) {
    return htmlspecialchars($tekst ?? '', ENT_QUOTES, 'UTF-8');
}

// Formatiranje cijene
function formatCijena($iznos) {
    return number_format((float)$iznos, 2, ',', '.') . ' €';
}

// Formatiranje datuma iz MySQL formata u dd.mm.yyyy
function formatDatum($datum) {
    if (!$datum) return '';
    $d = DateTime::createFromFormat('Y-m-d', $datum);
    return $d ? $d->format('d.m.Y.') : $datum;
}

// Da li je korisnik prijavljen
function jePrijavljen() {
    return isset($_SESSION['korisnik_id']);
}

// Da li je prijavljeni korisnik admin
function jeAdmin() {
    return jePrijavljen() && ($_SESSION['uloga'] ?? '') === 'admin';
}

// Preusmjeri na drugu stranicu i prekini izvršavanje
function preusmjeri($putanja) {
    header('Location: ' . $putanja);
    exit;
}

// Zaštita stranice - samo za prijavljene korisnike
function zahtijevajPrijavu() {
    if (!jePrijavljen()) {
        $_SESSION['poruka_greska'] = 'Moraš biti prijavljen da bi pristupio ovoj stranici.';
        preusmjeri('login.php');
    }
}

// Zaštita admin stranica
function zahtijevajAdmina() {
    if (!jeAdmin()) {
        preusmjeri('../login.php');
    }
}

// Izračunaj broj dana između dva datuma (minimum 1 dan)
function brojDana($datumOd, $datumDo) {
    $a = new DateTime($datumOd);
    $b = new DateTime($datumDo);
    $razlika = $a->diff($b)->days;
    return max(1, $razlika);
}

// Prikaz jednokratnih poruka (flash messages) iz sesije
function prikaziPoruke() {
    if (!empty($_SESSION['poruka_uspjeh'])) {
        echo '<div class="poruka poruka-uspjeh">' . h($_SESSION['poruka_uspjeh']) . '</div>';
        unset($_SESSION['poruka_uspjeh']);
    }
    if (!empty($_SESSION['poruka_greska'])) {
        echo '<div class="poruka poruka-greska">' . h($_SESSION['poruka_greska']) . '</div>';
        unset($_SESSION['poruka_greska']);
    }
}

// Status rezervacije -> labela i CSS klasa za bedž
function statusInfo($status) {
    $mapa = [
        'na cekanju'  => ['Na čekanju', 'bedz-zuti'],
        'potvrdjena'  => ['Potvrđena', 'bedz-zeleni'],
        'odbijena'    => ['Odbijena', 'bedz-crveni'],
        'zavrsena'    => ['Završena', 'bedz-sivi'],
        'otkazana'    => ['Otkazana', 'bedz-crveni'],
    ];
    return $mapa[$status] ?? [$status, 'bedz-sivi'];
}
