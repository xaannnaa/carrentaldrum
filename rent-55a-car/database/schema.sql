-- ============================================
-- DRUM | Rent a Car - šema baze podataka
-- ============================================

CREATE DATABASE IF NOT EXISTS drum_rentacar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE drum_rentacar;

-- ---------- KORISNICI ----------
CREATE TABLE korisnici (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(50) NOT NULL,
    prezime VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    lozinka VARCHAR(255) NOT NULL,
    telefon VARCHAR(30),
    uloga ENUM('korisnik','admin') NOT NULL DEFAULT 'korisnik',
    datum_registracije TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- KATEGORIJE VOZILA ----------
CREATE TABLE kategorije (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------- VOZILA ----------
CREATE TABLE vozila (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(100) NOT NULL,
    kategorija_id INT,
    godiste YEAR NOT NULL,
    gorivo ENUM('Benzin','Dizel','Hibrid','Elektricni') NOT NULL DEFAULT 'Dizel',
    mjenjac ENUM('Manuelni','Automatski') NOT NULL DEFAULT 'Manuelni',
    broj_sjedista TINYINT NOT NULL DEFAULT 5,
    cijena_po_danu DECIMAL(8,2) NOT NULL,
    slika VARCHAR(255) DEFAULT 'no-image.jpg',
    opis TEXT,
    dostupno TINYINT(1) NOT NULL DEFAULT 1,
    datum_dodavanja TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategorija_id) REFERENCES kategorije(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------- REZERVACIJE ----------
CREATE TABLE rezervacije (
    id INT AUTO_INCREMENT PRIMARY KEY,
    korisnik_id INT NOT NULL,
    vozilo_id INT NOT NULL,
    datum_preuzimanja DATE NOT NULL,
    datum_vracanja DATE NOT NULL,
    mjesto_preuzimanja VARCHAR(100) NOT NULL,
    napomena TEXT,
    ukupna_cijena DECIMAL(9,2) NOT NULL,
    status ENUM('na cekanju','potvrdjena','odbijena','zavrsena','otkazana') NOT NULL DEFAULT 'na cekanju',
    datum_kreiranja TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (korisnik_id) REFERENCES korisnici(id) ON DELETE CASCADE,
    FOREIGN KEY (vozilo_id) REFERENCES vozila(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- DEMO PODACI
-- ============================================

-- Admin nalog se NE ubacuje ovdje direktno (lozinka mora biti ispravno heširana
-- pomoću PHP password_hash funkcije). Pokreni jednom u browseru: create_admin.php
-- To će kreirati nalog: admin@drum.me / admin123 (obavezno promijeniti lozinku poslije).

INSERT INTO kategorije (naziv) VALUES
('Ekonomska'), ('Kompaktna'), ('SUV'), ('Limuzina'), ('Kombi');

INSERT INTO vozila (naziv, kategorija_id, godiste, gorivo, mjenjac, broj_sjedista, cijena_po_danu, slika, opis, dostupno) VALUES
('Fiat Punto', 1, 2019, 'Benzin', 'Manuelni', 5, 18.00, 'no-image.jpg', 'Ekonomično gradsko vozilo, idealno za kratke relacije i parkiranje u uskim ulicama.', 1),
('VW Golf 7', 2, 2020, 'Dizel', 'Manuelni', 5, 28.00, 'no-image.jpg', 'Pouzdan kompakt sa odličnom potrošnjom goriva, pogodan za duže putovanje.', 1),
('Škoda Octavia', 2, 2021, 'Dizel', 'Automatski', 5, 32.00, 'no-image.jpg', 'Prostran kompakt sa automatskim mjenjačem i velikim prtljažnikom.', 1),
('Dacia Duster', 3, 2022, 'Dizel', 'Manuelni', 5, 38.00, 'no-image.jpg', 'SUV pogodan za planinske puteve, sa pogonom 4x4 opcijom.', 1),
('VW Tiguan', 3, 2022, 'Dizel', 'Automatski', 5, 48.00, 'no-image.jpg', 'Moderan porodični SUV sa bogatom opremom i automatskim mjenjačem.', 1),
('Mercedes E-Klasa', 4, 2021, 'Dizel', 'Automatski', 5, 75.00, 'no-image.jpg', 'Luksuzna limuzina za poslovna putovanja i posebne prilike.', 1),
('VW Caddy', 5, 2020, 'Dizel', 'Manuelni', 7, 42.00, 'no-image.jpg', 'Kombi vozilo za veće grupe ili prevoz tereta.', 1);
