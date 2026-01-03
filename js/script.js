// JS PER HAMBURGER MENU
function hamburgerMenu() {
  const hamburger = document.getElementById('hamburger');
  const hamburgerChiuso = document.getElementById('hamburger-chiuso');
  const hamburgerAperto = document.getElementById('hamburger-aperto');

  const menu = document.getElementById('hamburger-menu');
  const content = document.getElementById('content-container');

  hamburger.addEventListener('click', function () {
    const aperto = hamburger.getAttribute('aria-expanded') === 'true';

    if(aperto) {
      hamburger.setAttribute('aria-expanded', 'false');
      hamburger.setAttribute('aria-label', 'Apri il menù di navigazione');

      hamburgerChiuso.classList.add('attivo');  
      hamburgerAperto.classList.remove('attivo'); 

      menu.hidden = true;
      menu.classList.remove('attivo');

      content.classList.remove('hamburger-attivo');
      content.classList.remove('attivo');
    } else {
      hamburger.setAttribute('aria-expanded', 'true');
      hamburger.setAttribute('aria-label', 'Chiudi il menù di navigazione');

      hamburgerChiuso.classList.remove('attivo'); 
      hamburgerAperto.classList.add('attivo');   

      menu.hidden = false;
      menu.classList.add('attivo');

      content.classList.add('hamburger-attivo');
      content.classList.add('attivo');
    }
  });
}

function initCarosello() {
  const thumbnails = document.querySelectorAll('.carosello-thumbnails img');
  const mainImage = document.querySelector('.carosello-principale img');
  const prevButton = document.getElementById('carosello-prev');
  const nextButton = document.getElementById('carosello-next');

  const total = thumbnails.length;
  let currentIndex = 0;

  if (!mainImage || total === 0) return;

  // 👉 se una sola immagine, nascondi frecce
  if (total === 1) {
    prevButton?.classList.add('nascosto');
    nextButton?.classList.add('nascosto');
  }

  function showImage(index) {
    currentIndex = index;
    mainImage.src = thumbnails[currentIndex].src;

    thumbnails.forEach(t => t.classList.remove('attiva'));
    thumbnails[currentIndex].classList.add('attiva');

    // Gestione frecce
    if (prevButton && nextButton) {
      prevButton.classList.toggle('nascosto', currentIndex === 0);
      nextButton.classList.toggle('nascosto', currentIndex === total - 1);
    }
  }

  // 👉 click miniature
  thumbnails.forEach((thumb, i) => {
    thumb.addEventListener('click', () => showImage(i));
  });

  // 👉 frecce
  prevButton?.addEventListener('click', () => {
    if (currentIndex > 0) showImage(currentIndex - 1);
  });

  nextButton?.addEventListener('click', () => {
    if (currentIndex < total - 1) showImage(currentIndex + 1);
  });

  // 👉 swipe mobile
  let startX = 0;

  mainImage.addEventListener('touchstart', e => {
    startX = e.touches[0].clientX;
  });

  mainImage.addEventListener('touchend', e => {
    const delta = e.changedTouches[0].clientX - startX;
    if (Math.abs(delta) > 50) {
      if (delta < 0 && currentIndex < total - 1) showImage(currentIndex + 1);
      if (delta > 0 && currentIndex > 0) showImage(currentIndex - 1);
    }
  });

  // inizializza
  showImage(0);
}

// JS PER IMMAGINI CON ALT IN PUBBLICA
function toggleMultipleAlt() {
  if(!document.getElementById("foto1") || !document.getElementById("alt1") || !document.getElementById("decorativa1")) {return}
  for (let i = 1; i <= 4; i++) {
    const fileInput = document.getElementById("foto" + i);
    const altInput = document.getElementById("alt" + i);
    const check = document.getElementById("decorativa" + i);
    
    // Gestione decorativa
    check.addEventListener("change", () => {
      if (check.checked) {
        altInput.value = "";
        altInput.disabled = true;
      } else {
        altInput.disabled = false;
      }
    });

    fileInput.addEventListener("change", () => {
      if (fileInput.files.length > 0) {
        altInput.disabled = false;
        check.disabled = false;

        const nextBlock = document.getElementById("img" + (i + 1));
        if (nextBlock) {
          nextBlock.style.display = "block";
          nextBlock.querySelectorAll("input").forEach(el => el.disabled = false);
        }
      }
    });
  }
}


// JS PER TOGGLE FILTRI
function toggleFiltri() {
  const toggle = document.getElementById("toggleFiltri");
  const filtri = document.getElementById("filtri");
  const chiudi = document.getElementById("chiudiFiltri");

  if(!toggle || !filtri || !chiudi) {return}
  toggle.addEventListener("click", () => {
    filtri.classList.add("attivo");
    toggle.setAttribute("aria-expanded", true);
  });

  chiudi.addEventListener("click", () => {
    filtri.classList.remove("attivo");
    toggle.setAttribute("aria-expanded", false);
  });
}

// JS PER TOGGLE FILTRI CATEGORIA
function toggleFiltriCategoria() {
  const filtroCategoria = document.getElementById('categoria');
  if(!filtroCategoria) return;

  const categorie = {
    Affitti: document.getElementById('filtri-affitti'),
    Esperimenti: document.getElementById('filtri-esperimenti'),
    Eventi: document.getElementById('filtri-eventi'),
    Ripetizioni: document.getElementById('filtri-ripetizioni')
  }

  function aggiornaFiltri() {
    Object.values(categorie).forEach(div => {
      div.classList.add('nascondi-filtri');
      setDisabled(div, true);
    });

    const selezionato = categorie[filtroCategoria.value];
    if(selezionato) {
      selezionato.classList.remove('nascondi-filtri');
      setDisabled(selezionato, false);
    }
  }

  aggiornaFiltri();
  filtroCategoria.addEventListener('change', aggiornaFiltri);

}

// JS PER TOGGLE PUBBLICA CATEGORIA
function togglePubblicaCategoria() {

  const sceltaCategoria = document.getElementById('categoria-campi');
  if (!sceltaCategoria) return;

  const categorie = {
    Affitti: document.getElementById('campi-affitti'),
    Esperimenti: document.getElementById('campi-esperimenti'),
    Eventi: document.getElementById('campi-eventi'),
    Ripetizioni: document.getElementById('campi-ripetizioni')
  };

  function aggiornaCampi() {
    // Nascondi e disabilita tutti
    Object.values(categorie).forEach(div => {
      div.classList.add('nascondi-campi');
      setDisabled(div, true);
    });

    // Mostra e abilita quello selezionato
    const selezionato = categorie[sceltaCategoria.value];
    if (selezionato) {
      selezionato.classList.remove('nascondi-campi');
      setDisabled(selezionato, false);
    }
  }

  // Stato iniziale
  aggiornaCampi();

  // Cambio categoria
  sceltaCategoria.addEventListener('change', aggiornaCampi);
}

function setDisabled(container, disabled) {
  const fields = container.querySelectorAll('input, select, textarea');
  fields.forEach(field => {
    field.disabled = disabled;
  });
}

function togglePasswordVisibility(checkboxId, inputId) {
    const checkbox = document.getElementById(checkboxId);
    const input = document.getElementById(inputId);

    if (!checkbox || !input) return;

    checkbox.addEventListener('change', () => {
        input.type = checkbox.checked ? 'text' : 'password';
    });
}

/*
// CONVERSIONE PNG/WEBP → JPG (solo se possibile)
function convertiImmaginiInJPG() {
  const inputs = document.querySelectorAll("input[type='file'][id^='foto']");

  // Se il browser non supporta canvas → non convertire
  if (!window.HTMLCanvasElement) return;

  inputs.forEach(input => {
    input.addEventListener("change", async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      // Se è già JPG → non serve convertire
      if (file.type === "image/jpeg") return;

      // Converte solo PNG o WebP
      if (file.type !== "image/png" && file.type !== "image/webp") return;

      const img = new Image();
      img.src = URL.createObjectURL(file);

      img.onload = () => {
        const canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);

        canvas.toBlob((blob) => {
          if (!blob) return; // Se fallisce → lascia il file originale

          const jpgFile = new File(
            [blob],
            file.name.replace(/\.\w+$/, ".jpg"),
            { type: "image/jpeg", lastModified: Date.now() }
          );

          // Sostituisce il file nel form
          const dt = new DataTransfer();
          dt.items.add(jpgFile);
          e.target.files = dt.files;

        }, "image/jpeg", 0.85);
      };
    });
  });
}
*/

document.addEventListener('DOMContentLoaded', function() {
  hamburgerMenu();
  initCarosello();
  toggleFiltri();
  toggleMultipleAlt();
  toggleFiltriCategoria();
  togglePubblicaCategoria();
  togglePasswordVisibility('mostraPassword', 'password');
  togglePasswordVisibility('mostraConfermaPassword', 'confermaPassword');
  // convertiImmaginiInJPG();
});