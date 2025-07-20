// Formatteur automatique de date utilisant la langue de la page
const formatter = new Intl.DateTimeFormat(document.documentElement.lang, {
  weekday: "long", month: "long", day: "numeric"
});

function addDate(date, selected = false) {
  const opt = document.createElement('option');
  opt.value = date; // YYYY-MM-DD
  opt.textContent = formatter.format(new Date(date));
  opt.classList.add("dt");
  if (selected) opt.selected = true
  dtInput.appendChild(opt);
}

function addHour(heure, selected = false) {
  const opt = document.createElement('option');
  opt.value = heure; // hh:mm:ss
  opt.textContent = heure.slice(0, 5); // hh:mm
  opt.classList.add("tm");
  if (selected) opt.selected = true
  tmInput.appendChild(opt);
}

window.addDate = addDate;
window.addHour = addHour;

document.addEventListener('DOMContentLoaded', () => {
  const nbInput = document.getElementById('nbInput');
  if (nbInput == null) return;
  const dtInput = document.getElementById('dtInput');
  const tmInput = document.getElementById('tmInput');

  let currDate = null;
  let currTime = null;

  updateDates();

  nbInput.addEventListener('change', () => {
    updateDates();
  });

  dtInput.addEventListener('change', () => {
    currDate = dtInput.value;
    updateHeures();
  });

  tmInput.addEventListener('change', () => {
    currTime = tmInput.value;
  });

  function updateDates() {
    var nb = parseInt(nbInput.value) || 1;
    // On utilise currDate pour sauvegarder le choix utilisateur
    if (currDate == null) currDate = dtInput.value;

    // Retirer toutes les dates du select
    var dts = document.querySelectorAll(".dt");
    dts.forEach(dt => {
      dtInput.removeChild(dt);
    })

    fetch('/api/free/' + nb)
    .then(r => r.json())
    .then(dates => {
      dates.forEach(date => {
        addDate(date);
      });
      if (dates.includes(currDate)) {
        // Si toujours valide, on remet le choix utilisateur
        dtInput.value = currDate;
      }
      else {
        // Sinon, il devra en sélectionner un nouveau
        currDate = null;
      }
      updateHeures();
    });
  }

  function updateHeures() {
    var nb = parseInt(nbInput.value) || 1;
    // On utilise currTime pour sauvegarder le choix utilisateur
    if (currTime == null) currTime = tmInput.value;

    // Retirer tous les créneaux du select
    var tms = document.querySelectorAll(".tm");
    tms.forEach(tm => {
      tmInput.removeChild(tm);
    })

    if (currDate == null) currDate = dtInput.value;
    // Créneau ne peut exister sans date 
    if (currDate == "") {
      currTime = null;
      return;
    }

    fetch(`/api/free/${nb}/${currDate}`)
    .then(r => r.json())
    .then(heures => {
      heures.forEach(heure => {
        addHour(heure);
      });
      if (heures.includes(currTime)) {
        // Si toujours valide, on remet le choix utilisateur
        tmInput.value = currTime;
      } else {
        // Sinon, il devra en sélectionner un nouveau
        currTime = null;
      }
    });
  }
});