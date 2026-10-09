# DRUM | Rent a Car — uputstvo za instalaciju

Sajt je napravljen u čistom PHP-u (bez frameworka), MySQL bazi, HTML/CSS/JS.

## 1. Instalacija XAMPP-a (PHP + MySQL + Apache)

1. Preuzmi XAMPP sa https://www.apachefriends.org (izaberi verziju za svoj OS).
2. Instaliraj ga (Next → Next → Install).
3. Otvori **XAMPP Control Panel** i klikni **Start** pored:
   - **Apache**
   - **MySQL**

## 2. Postavljanje fajlova sajta

1. Otvori folder gdje je XAMPP instaliran, npr.:
   - Windows: `C:\xampp\htdocs\`
   - macOS: `/Applications/XAMPP/htdocs/`
   - Linux: `/opt/lampp/htdocs/`
2. Otpakuj (raspakuj) ceo `rent-a-car` folder direktno u `htdocs`, tako da putanja izgleda ovako:
   `htdocs/rent-a-car/index.php`

## 3. Kreiranje baze podataka

1. Otvori browser i idi na: `http://localhost/phpmyadmin`
2. Klikni na tab **SQL** (gore).
3. Otvori fajl `database/schema.sql` (na tvom računaru), kopiraj sav sadržaj i nalijepi ga u to polje.
4. Klikni **Go / Izvrši**. Ovo će kreirati bazu `drum_rentacar` sa svim tabelama i demo vozilima.

> Ako ti `root` korisnik MySQL-a ima lozinku (rijetko kod podrazumijevane XAMPP instalacije), otvori `config/db.php` i upiši je u `DB_PASS`.

## 4. Kreiranje admin naloga

1. Idi na: `http://localhost/rent-a-car/create_admin.php`
2. Stranica će ispisati poruku da je admin nalog kreiran:
   - Email: `admin@drum.me`
   - Lozinka: `admin123`
3. **Odmah nakon toga obriši fajl `create_admin.php`** iz foldera (iz bezbjednosnih razloga — da niko drugi ne može ponovo da ga pokrene).

## 5. Pokretanje sajta

Otvori u browseru: `http://localhost/rent-a-car/`

- Javni sajt: pregled vozila, registracija, prijava, rezervacija, "Moje rezervacije"
- Admin panel: `http://localhost/rent-a-car/admin/` (prijavi se kao admin na `login.php`, automatski te vodi u admin panel)

## 6. Struktura projekta

```
rent-a-car/
├── database/schema.sql       → SQL šema + demo podaci
├── config/db.php             → konekcija na bazu (izmijeni ovdje kredencijale ako treba)
├── includes/                 → zajednički header/footer/funkcije za javni sajt
├── css/style.css             → glavni stilovi
├── css/admin.css             → stilovi admin panela
├── js/main.js                → mobilni meni + live kalkulacija cijene rezervacije
├── uploads/cars/             → ovdje se čuvaju slike vozila (upload preko admin panela)
├── index.php, cars.php, car-details.php, booking.php, my-bookings.php,
│   login.php, register.php, logout.php, contact.php   → javne stranice
└── admin/                    → admin panel (zaštićen, samo za uloga='admin')
    ├── index.php              → dashboard sa statistikom
    ├── cars.php / car-edit.php / car-delete.php  → CRUD za vozila (sa uploadom slike)
    ├── bookings.php           → pregled i promjena statusa rezervacija
    └── users.php              → pregled registrovanih korisnika
```

## 7. Najčešći problemi

- **"Greška pri povezivanju na bazu"** → MySQL u XAMPP-u nije pokrenut, ili je pogrešna lozinka u `config/db.php`.
- **Bijela/prazna stranica** → uključi prikaz grešaka privremeno dodavanjem na vrh `config/db.php`:
  `error_reporting(E_ALL); ini_set('display_errors', 1);`
- **Slike vozila se ne vide** → ako nisi otpremio sliku preko admin panela, sajt automatski prikazuje DRUM placeholder (potrebna internet konekcija u browseru za taj placeholder).

## 8. Sledeći koraci (ideje za nadogradnju)

- Slanje email potvrde nakon rezervacije (npr. PHPMailer + SMTP)
- Online plaćanje (npr. preko platnog procesora)
- Filtriranje vozila po cijenovnom rangu
- Ocjene i komentari korisnika
