// JS PER HAMBURGER MENU
function hamburgerMenu() {
  const hamburger = document.getElementById('hamburger');
  const menu = document.getElementById('menu');
  const chiuso = document.getElementById('hamburger-chiuso');
  const aperto = document.getElementById('hamburger-aperto');
  const container = document.getElementById('content-container');

  hamburger.addEventListener('click', () => {
    const isOpen = hamburger.getAttribute('aria-expanded') === 'true';

    hamburger.setAttribute('aria-expanded', String(!isOpen));
    hamburger.setAttribute(
      'aria-label',
      isOpen ? 'Apri il menù di navigazione' : 'Chiudi il menù di navigazione'
    );

    menu.classList.toggle('attivo');
    chiuso.classList.toggle('attivo');
    aperto.classList.toggle('attivo');
    container.classList.toggle('attivo');
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

  // se una sola immagine, nascondi frecce
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

  // click miniature
  thumbnails.forEach((thumb, i) => {
    thumb.addEventListener('click', () => showImage(i));
  });

  // frecce
  prevButton?.addEventListener('click', () => {
    if (currentIndex > 0) showImage(currentIndex - 1);
  });

  nextButton?.addEventListener('click', () => {
    if (currentIndex < total - 1) showImage(currentIndex + 1);
  });

  // swipe mobile
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
          nextBlock.style.display = "flex";
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
    if(categorie.length == 1) return; // Significa che sono su modificaAnnuncio
    Object.values(categorie).forEach(div => {
      if(div != null) {
        div.classList.add('nascondi-campi');
        setDisabled(div, true);
      }
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

function gestisciFrecciaSu() {
    const freccia = document.querySelector(".freccia-su");
    const footer = document.querySelector("footer");

    if (!freccia || !footer) return;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                freccia.classList.add("in-footer");
            } else {
                freccia.classList.remove("in-footer");
            }
        });
    }, { threshold: 0.1 });

    observer.observe(footer);
}

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