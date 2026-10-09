// ===================== MOBILNI MENI =====================
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburgerDugme');
    const nav = document.getElementById('glavnaNav');
    if (hamburger && nav) {
        hamburger.addEventListener('click', function () {
            nav.classList.toggle('otvoreno');
        });
    }

    // ===================== KALKULACIJA CIJENE REZERVACIJE =====================
    const datumOd = document.getElementById('datum_preuzimanja');
    const datumDo = document.getElementById('datum_vracanja');
    const prikazDana = document.getElementById('prikazBrojDana');
    const prikazUkupno = document.getElementById('prikazUkupnaCijena');
    const dugmeRezervisi = document.getElementById('dugmeRezervisi');

    if (datumOd && datumDo && prikazUkupno) {
        const cijenaPoDanu = parseFloat(prikazUkupno.dataset.cijenaPoDanu || '0');

        // Ne dozvoli biranje datuma u prošlosti
        const danas = new Date().toISOString().split('T')[0];
        datumOd.min = danas;
        datumDo.min = danas;

        function preracunaj() {
            if (!datumOd.value || !datumDo.value) return;

            datumDo.min = datumOd.value;

            const od = new Date(datumOd.value);
            const dox = new Date(datumDo.value);
            let brojDana = Math.round((dox - od) / (1000 * 60 * 60 * 24));

            if (brojDana < 1) {
                prikazDana.textContent = '–';
                prikazUkupno.textContent = '0,00 €';
                if (dugmeRezervisi) dugmeRezervisi.disabled = true;
                return;
            }

            const ukupno = brojDana * cijenaPoDanu;
            prikazDana.textContent = brojDana + (brojDana === 1 ? ' dan' : ' dana');
            prikazUkupno.textContent = ukupno.toLocaleString('sr-RS', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
            if (dugmeRezervisi) dugmeRezervisi.disabled = false;
        }

        datumOd.addEventListener('change', preracunaj);
        datumDo.addEventListener('change', preracunaj);
        preracunaj();
    }

    // ===================== POTVRDA PRIJE BRISANJA (admin) =====================
    document.querySelectorAll('.zahtijeva-potvrdu').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const poruka = el.dataset.potvrda || 'Da li si siguran/sigurna?';
            if (!confirm(poruka)) {
                e.preventDefault();
            }
        });
    });
});
