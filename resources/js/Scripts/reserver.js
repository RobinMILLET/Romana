document.addEventListener('DOMContentLoaded', () => {
  const nbInput = document.getElementById('nbInput');
  const dtInput = document.getElementById('dtInput');
  const tmInput = document.getElementById('tmInput');

  const formatter = new Intl.DateTimeFormat(navigator.language, {
    weekday: "long", year: "numeric", month: "long", day: "numeric"
  });

  let maxNb = 25;
  let currDate = null;
  let currHour = null;

  fetch('/api/const')
  .then(r => r.json())
  .then(data => {
    maxNb = data.reservation_personnes_max || maxNb;
    nbInput.max = maxNb;
    nbInput.value = 1;
    updateDates(1);
  });

  nbInput.addEventListener('change', () => {
    let val = parseInt(nbInput.value) || 1;
    if(val > maxNb) {
      val = maxNb;
      nbInput.value = val;
    } else if(val < 1) {
      val = 1;
      nbInput.value = val;
    }
    updateDates();
  });

  dtInput.addEventListener('change', () => {
    currDate = dtInput.value;
    updateHeures();
  });

  tmInput.addEventListener('change', () => {
    currHour = tmInput.value;
  });

  function updateDates() {
    var nb = parseInt(nbInput.value) || 1;
    if (currDate == null) currDate = dtInput.value;

    var dts = document.querySelectorAll(".dt");
    dts.forEach(dt => {
      dtInput.removeChild(dt);
    })

    fetch('/api/free/' + nb)
    .then(r => r.json())
    .then(dates => {
      dates.forEach(date => {
        const opt = document.createElement('option');
        opt.value = date;
        opt.textContent = formatter.format(new Date(date));
        opt.classList.add("dt");
        dtInput.appendChild(opt);
      });
      if (dates.includes(currDate)) {
        dtInput.value = currDate;
      }
      else {
        currDate = null;
      }
      updateHeures();
    });
  }

  function updateHeures() {
    var nb = parseInt(nbInput.value) || 1;
    if (currHour == null) currHour = tmInput.value;

    var tms = document.querySelectorAll(".tm");
    tms.forEach(tm => {
      tmInput.removeChild(tm);
    })

    if (currDate == null) currDate = dtInput.value;
    if (currDate == "null") {
      currHour = null;
      return;
    }

    fetch(`/api/free/${nb}/${currDate}`)
    .then(r => r.json())
    .then(heures => {
      heures.forEach(heure => {
        const opt = document.createElement('option');
        opt.value = heure;
        opt.textContent = heure.slice(0, 5);
        if (document.documentElement.lang !== "fr")
          opt.textContent += " (UTC+2)";
        opt.classList.add("tm");
        tmInput.appendChild(opt);
      });
      if (heures.includes(currHour)) {
        tmInput.value = currHour;
      } else {
        currHour = null;
      }
    });
  }
});