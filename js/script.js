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

function toggleFiltriAccessibile() {
    const toggle = document.getElementById("toggleFiltri");
    const filtri = document.getElementById("filtri");
    const chiudi = document.getElementById("chiudiFiltri");
    const overlay = document.getElementById("overlay-filtri");
    const contenuto = document.getElementById("contenuto");

    if (!toggle || !filtri || !chiudi || !overlay || !contenuto) return;

    let lastFocusedElement = null;

    const focusableSelectors = `
        button,
        input,
        select,
        textarea,
        a[href],
        [tabindex]:not([tabindex="-1"])
    `;

    function getFocusable() {
        return filtri.querySelectorAll(focusableSelectors);
    }

    function apriPannello() {
        lastFocusedElement = document.activeElement;

        filtri.classList.add("attivo");
        overlay.hidden = false;

        toggle.setAttribute("aria-expanded", "true");
        contenuto.setAttribute("aria-hidden", "true");
        document.body.classList.add("no-scroll");

        const titolo = filtri.querySelector("h2");
        titolo.focus();
    }

    function chiudiPannello() {
        filtri.classList.remove("attivo");
        overlay.hidden = true;

        toggle.setAttribute("aria-expanded", "false");
        contenuto.removeAttribute("aria-hidden");
        document.body.classList.remove("no-scroll");

        lastFocusedElement?.focus();
    }

    toggle.addEventListener("click", apriPannello);
    chiudi.addEventListener("click", chiudiPannello);
    overlay.addEventListener("click", chiudiPannello);

    // ESC per chiudere
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && filtri.classList.contains("attivo")) {
            chiudiPannello();
        }
    });

    // Focus trap
    filtri.addEventListener("keydown", (e) => {
        if (e.key !== "Tab") return;

        const focusable = getFocusable();
        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });

    // Chiudi quando si applicano i filtri
    filtri.querySelector("form").addEventListener("submit", () => {
        chiudiPannello();
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

function initDeleteConfirmation() {
  const form = document.getElementById('delete-form');

  if (!form) return;

  form.addEventListener('submit', function (e) {
    const conferma = confirm(
      'Sei sicuro di voler cancellare questo annuncio?\n\nQuesta azione è irreversibile.'
    );

    if (!conferma) {
      e.preventDefault();
    }
  });
}

function maxFileSizeAlert() {

  const fileInputs = [
    document.getElementById('foto1'),
    document.getElementById('foto2'),
    document.getElementById('foto3'),
    document.getElementById('foto4')
  ];

  // Limite singola immagine
  const MAX_SIZE = 1 * 1024 * 1024; // 1 MB in byte

  // Aggiungi evento change a ciascun input
  for (const input of fileInputs) {
    if(input == null) return;
    input.addEventListener('change', function () {
      if (input.files.length > 0 && input.files[0].size > MAX_SIZE) {
        alert("Questa immagine è troppo grande. Massimo consentito: 1 MB.");
        // Resetta il file selezionato
        input.value = '';
      }
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
    hamburgerMenu();
    initCarosello();
    toggleFiltri();
    toggleFiltriAccessibile(); 
    toggleMultipleAlt();
    toggleFiltriCategoria();
    togglePubblicaCategoria();
    togglePasswordVisibility('mostraPassword', 'password');
    togglePasswordVisibility('mostraConfermaPassword', 'confermaPassword');
    initDeleteConfirmation();
    maxFileSizeAlert();

    // --- VALIDAZIONE SOLO PER I FORM CON data-validate ---
    const form = document.querySelector("form[data-validate]");
    if (!form) return;

    // Errori server-side
    const globalImageError = document.querySelector("#errore-immagini-globali .msgErrore");
    const fieldError = form.querySelector(".msgErrore[role='alert']:not(#errore-immagini-globali .msgErrore)");

    if (globalImageError && !fieldError) {
        // Solo per errori immagini -> focus sul primo file
        const fileInput = document.querySelector("#foto1");
        if (fileInput) fileInput.focus();
    } else if (fieldError) {
        // Errori su campi normali -> focus sull'input associato
        const field = fieldError.closest(".form-div")?.querySelector("input, select, textarea");
        if (field) field.focus();
    }

    // blur/input
    form.addEventListener("blur", (e) => {
        if (isValidatable(e.target)) validateField(e.target);
    }, true);

    form.addEventListener("input", (e) => {
        if (isValidatable(e.target)) removeError(e.target);
    }, true);

    form.addEventListener("submit", (e) => {
        let ok = true;
        const fields = form.querySelectorAll("input, select, textarea");

        fields.forEach(f => {
            if (isValidatable(f)) {
                const valid = validateField(f);
                if (!valid) ok = false;
            }
        });

        if (!ok) {
            e.preventDefault();
            const first = form.querySelector(".msgErrore");
            if (first) {
                const field = first.closest(".form-div")?.querySelector("input, select, textarea");
                if (field) field.focus();
            }
        }
    });
});

// funzioni per validabilità e rimozione errori
function isValidatable(field) {
    if (!field.id) return false;
    if (field.type === "file") return false;
    if (field.id.startsWith("alt") || field.id.startsWith("decorativa")) return false;
    return true;
}

function removeError(field) {
    field.removeAttribute("aria-invalid");

    const old = field.parentNode.querySelector(".messaggi-errore-form");
    if (old) old.remove();

    const described = field.getAttribute("aria-describedby");
    if (described) {
        const cleaned = described
            .split(" ")
            .filter(id => !id.includes("-errore"))
            .join(" ");

        if (cleaned) field.setAttribute("aria-describedby", cleaned);
        else field.removeAttribute("aria-describedby");
    }
}

// validazione campo
function validateField(field) {
    removeError(field);

    const value = field.value.trim();
    let errors = [];

    // campi richiesti
    if (field.hasAttribute("required") && value === "") {
        errors.push("Questo campo è obbligatorio.");
    }

    switch (field.id) {
        case "nome":
            if (!/^[a-zA-ZÀ-ÿ\s]{2,30}$/.test(value)) {
                errors.push("Il nome deve contenere solo lettere e almeno 2 caratteri.");
            }
            break;

        case "cognome":
            if (!/^[a-zA-ZÀ-ÿ\s]{2,30}$/.test(value)) {
                errors.push("Il cognome deve contenere solo lettere e almeno 2 caratteri.");
            }
            break;

        case "email":
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                errors.push("Inserisci un'<span lang='en'>email</span> valida nel formato nome@dominio.it");
            }
            break;

        case "password":
            if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(value)) {
                errors.push("La <span lang='en'>password</span> non rispetta i requisiti minimi.");
            }
            break;

        case "confermaPassword":
            const pass = document.getElementById("password").value.trim();
            if (value !== pass) {
                errors.push("Le <span lang='en'>password</span> non coincidono.");
            }
            break;

        case "citta":
            const list = field.list;
            let found = false;

            if (list) {
                for (const opt of list.options) {
                    if (opt.value.trim().toLowerCase() === value.toLowerCase()) {
                        found = true;
                        break;
                    }
                }
            }
            if (!found) errors.push("La città inserita non è valida.");
            break;

        case "consenso-email":
            if (!field.checked) errors.push("Devi acconsentire all'uso pubblico dell'<span lang='en'>email</span>.");
            break;
    }

    if (errors.length > 0) {
        renderError(field, errors);
        return false;
    }

    return true;
}

//funzione di rendering degli errori
function renderError(field, errors) {
    field.setAttribute("aria-invalid", "true");

    const box = document.createElement("ul");
    box.className = "riquadro-spieg messaggi-errore-form";

    errors.forEach(err => {
        const li = document.createElement("li");
        li.className = "msgErrore";
        li.id = `errore-${field.id}`;
        li.setAttribute("role", "alert");   
        li.textContent = err;
        box.appendChild(li);
    });

    field.parentNode.appendChild(box);
    field.setAttribute("aria-describedby", `errore-${field.id}`);

    // focus con selezione solo se è la prima volta che compare l'errore sul campo selezioanto
    if (!field.dataset.errorFocused) {
        field.focus();
        if (field.select) field.select();
        field.dataset.errorFocused = "true";
    }
}
