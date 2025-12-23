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

// JS PER CAROSELLO MINIATURE CLICCABILI
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

/*
//JS PER BOTTONI CAROSELLO (loop)
function slideCarosello()
{
  const prevButton = document.getElementById("carosello-prev");
  const nextButton = document.getElementById("carosello-next");
  const arrimmSec = document.querySelectorAll('.carosello-thumbnails img');
  const immPrinc = document.querySelector('.carosello-principale img');
  let index = 0;
  const dim = arrimmSec.length;


  prevButton.addEventListener('click', () => {

    if(index == 0)
    {
      index = dim-1;
      immPrinc.src = arrimmSec[index].src;
      arrimmSec.forEach(img => img.classList.remove('attiva'));
      arrimmSec[index].classList.add('attiva');
    }  
    else
    {
      index = index-1;
      immPrinc.src = arrimmSec[index].src;
      arrimmSec.forEach(img => img.classList.remove('attiva'));
      arrimmSec[index].classList.add('attiva');      
    }

  });

  nextButton.addEventListener('click', () => {

    if(index == dim-1)
    {
      index = 0;
      immPrinc.src = arrimmSec[index].src;
      arrimmSec.forEach(img => img.classList.remove('attiva'));
      arrimmSec[index].classList.add('attiva');       
    }  
    else
    {
      index = index+1;
      immPrinc.src = arrimmSec[index].src;
      arrimmSec.forEach(img => img.classList.remove('attiva'));
      arrimmSec[index].classList.add('attiva');
    }
  });

}
*/

// JS PER BOTTONI CAROSELLO BLOCCATO
function slideCarosello() {
    const prevButton = document.getElementById("carosello-prev");
    const nextButton = document.getElementById("carosello-next");
    const arrimmSec = document.querySelectorAll('.carosello-thumbnails img');
    const immPrinc = document.querySelector('.carosello-principale img');
    let index = 0;
    const dim = arrimmSec.length;

    if (prevButton === null || nextButton === null || arrimmSec.length === 0 || immPrinc === null) {
      return; // Esci se gli elementi non sono trovati
    }
    function showImage(i) {
      index = i;
      immPrinc.src = arrimmSec[index].src;
      arrimmSec.forEach(img => img.classList.remove('attiva'));
      arrimmSec[index].classList.add('attiva');

      prevButton.classList.remove("nascosto", "attivo");
      nextButton.classList.remove("nascosto", "attivo")

      if (index === 0) {
        prevButton.classList.add("nascosto");  
        nextButton.classList.add("attivo");   
      } else if (index === dim - 1) {
        nextButton.classList.add("nascosto");   
        prevButton.classList.add("attivo");   
      } else {
        prevButton.classList.add("attivo");
        nextButton.classList.add("attivo");
      }
    }

  showImage(0);

  prevButton.addEventListener('click', () => {
    if (index > 0) {
      showImage(index - 1);
    }
  });

  nextButton.addEventListener('click', () => {
    if (index < dim - 1) {
      showImage(index + 1);
    }
  });

  //swipe da telefono
  let startTouch = 0;
  immPrinc.addEventListener('touchstart', e => {
      startTouch = e.touches[0].clientX;
  });

  immPrinc.addEventListener('touchend', e => {
    const endTouch = e.changedTouches[0].clientX;
    const deltaTouch = endTouch - startTouch;

    if (Math.abs(deltaTouch) > 50) { 
        if (deltaTouch < 0 && index < dim - 1) {
          showImage(index + 1);
        } else if (deltaTouch > 0 && index > 0) {
          showImage(index - 1);
        }
    }
  });
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
    const filtriEventi = document.getElementById('filtri-eventi');
    const filtriEsperimenti = document.getElementById('filtri-esperimenti');
    const filtriAffitti = document.getElementById('filtri-affitti');
    const filtriRipetizioni = document.getElementById('filtri-ripetizioni');

    // Nascondi tutti 
    filtriEventi.classList.add('nascondi-filtri');
    filtriEsperimenti.classList.add('nascondi-filtri');
    filtriAffitti.classList.add('nascondi-filtri');
    filtriRipetizioni.classList.add('nascondi-filtri');

    // Mostra solo quello selezionato
    if (sceltaCategoria.value === 'Eventi') {
      filtriEventi.classList.remove('nascondi-filtri');
    } else if (sceltaCategoria.value === 'Esperimenti') {
      filtriEsperimenti.classList.remove('nascondi-filtri');
    } else if (sceltaCategoria.value === 'Affitti') {
      filtriAffitti.classList.remove('nascondi-filtri');
    } else if (sceltaCategoria.value === 'Ripetizioni') {
      filtriRipetizioni.classList.remove('nascondi-filtri');
    }
    
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
}

// JS PER TOGGLE PUBBLICA CATEGORIA
function togglePubblicaCategoria() {

  const sceltaCategoria = document.getElementById('categoria-campi');
  if (!sceltaCategoria) return;

  const categorie = {
    Eventi: document.getElementById('campi-eventi'),
    Esperimenti: document.getElementById('campi-esperimenti'),
    Affitti: document.getElementById('campi-affitti'),
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


document.addEventListener('DOMContentLoaded', function() {
  hamburgerMenu();
  caroselloChangeImage();
  toggleFiltri();
  toggleMultipleAlt();
  toggleFiltriCategoria();
  togglePubblicaCategoria();
  slideCarosello();
});