function hamburgerMenu() {
                const hamburger = document.getElementById('hamburger');
                const hamburgerChiuso = document.querySelectorAll('#hamburger > span')[0];
                const hamburgerAperto = document.querySelectorAll('#hamburger > span')[1];


                let menu = document.getElementById('hamburger-menu')
                // let header = document.getElementsByClassName('header-container')[0]

                hamburger.addEventListener('click', function () {
                    hamburgerChiuso.classList.toggle('active');
                    hamburgerAperto.classList.toggle('active');

                    hamburger.ariaPressed === "true" ? hamburger.ariaPressed = "false" : hamburger.ariaPressed = "true";

                    menu.classList.toggle('active');

                    /*
                    if (menu.classList.contains('active')) {
                        if (header.classList.contains('active') && window.scrollY < 30) {
                            header.classList.remove('active')
                        }
                        menu.classList.remove('active')
                        header.classList.remove('hamburger-active')
                    } else {
                        if (!header.classList.contains('active')) {
                            header.classList.add('active')
                        }
                        menu.classList.add('active')
                    }
                    */
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
