// JS PER HAMBURGER MENU
function hamburgerMenu() {
  const hamburger = document.getElementById('hamburger');
  const hamburgerChiuso = document.querySelectorAll('#hamburger > span')[0];
  const hamburgerAperto = document.querySelectorAll('#hamburger > span')[1];


  let menu = document.getElementById('hamburger-menu')
  let content = document.getElementsByClassName('content-container')[0]

  hamburger.addEventListener('click', function () {
      hamburgerChiuso.classList.toggle('attivo');
      hamburgerAperto.classList.toggle('attivo');

      hamburger.ariaPressed === "true" ? hamburger.ariaPressed = "false" : hamburger.ariaPressed = "true";

      // menu.classList.toggle('attivo');

      if (menu.classList.contains('attivo')) {
          if (content.classList.contains('attivo') && window.scrollY < 30) {
              content.classList.remove('attivo')
          }
          menu.classList.remove('attivo')
          content.classList.remove('hamburger-attivo')
      } else {
          if (!content.classList.contains('attivo')) {
              content.classList.add('attivo')
          }
          menu.classList.add('attivo')
      }
  })
}

// JS PER CAROSELLO IMMAGINI
function caroselloChangeImage() {
  const thumbnails = document.querySelectorAll('.carosello-thumbnails img');
  const mainImage = document.querySelector('.carosello-principale img');

  if(thumbnails.length > 0 && mainImage) {
      thumbnails.forEach(thumb => {
          thumb.addEventListener('click', () => {
          mainImage.src = thumb.src;
          thumbnails.forEach(t => t.classList.remove('attiva'));
          thumb.classList.add('attiva');
          });
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

// JS PER TOGGLE FILTRI CATEGORIA
function toggleFiltriCategoria() {
  // SEZIONE PER NASCONDERE I FILTRI RISPETTIVI PER OGNI CATEGORIA
  const sceltaCategoria = document.getElementById('categoria');
  if(sceltaCategoria)
  {
      sceltaCategoria.addEventListener('change', function() {
          const filtriEventi = document.getElementById('filtri-eventi');
          const filtriEsperimenti = document.getElementById('filtri-esperimenti');
          const filtriAffitti = document.getElementById('filtri-affitti');
          const filtriRipetizioni = document.getElementById('filtri-ripetizioni');

          //console.log(this.value);
          // Nascondi tutti 
          filtriEventi.classList.add('nascondi-filtri');
          filtriEsperimenti.classList.add('nascondi-filtri');
          filtriAffitti.classList.add('nascondi-filtri');
          filtriRipetizioni.classList.add('nascondi-filtri');

          // Mostra solo quello selezionato
          if (this.value === 'Eventi') {
              filtriEventi.classList.remove('nascondi-filtri');
          } else if (this.value === 'Esperimenti') {
              filtriEsperimenti.classList.remove('nascondi-filtri');
          } else if (this.value === 'Affitti') {
              filtriAffitti.classList.remove('nascondi-filtri');
          } else if (this.value === 'Ripetizioni') {
              filtriRipetizioni.classList.remove('nascondi-filtri');
          }
      });
  }

  // SEZIONE PER NASCONDERE I CAMPI RISPETTIVI PER OGNI CATEGORIA IN PUBBLICA
  if(sceltaCategoria)
  {
      sceltaCategoria.addEventListener('change', function() {
          const campiEventi = document.getElementById('campi-eventi');
          const campiEsperimenti = document.getElementById('campi-esperimenti');
          const campiAffitti = document.getElementById('campi-affitti');
          const campiRipetizioni = document.getElementById('campi-ripetizioni');

          //console.log(this.value);

          // Nascondi tutti 
          campiEventi.classList.add('nascondi-campi');
          campiEsperimenti.classList.add('nascondi-campi');
          campiAffitti.classList.add('nascondi-campi');
          campiRipetizioni.classList.add('nascondi-campi');

          // Mostra solo quello selezionato
          if (this.value === 'Eventi') {
              campiEventi.classList.remove('nascondi-campi');
          } else if (this.value === 'Esperimenti') {
              campiEsperimenti.classList.remove('nascondi-campi');
          } else if (this.value === 'Affitti') {
              campiAffitti.classList.remove('nascondi-campi');
          } else if (this.value === 'Ripetizioni') {
              campiRipetizioni.classList.remove('nascondi-campi');
          }
      });
  }
}

document.addEventListener('DOMContentLoaded', function() {
  hamburgerMenu();
  caroselloChangeImage();
  toggleFiltri();
  toggleMultipleAlt();
  toggleFiltriCategoria();
});