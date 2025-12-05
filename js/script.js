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
hamburgerMenu();

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

document.addEventListener("DOMContentLoaded", () => {
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
});
