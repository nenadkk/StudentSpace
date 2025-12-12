* inizialmente era stata aggiunta una classe **<span class="enfasi"></span>** per parole chiave all'interno della pagina, successivamente sostituita da <strong></strong> in quanto ciò ne aumenta l'importanza per i browser
* per garantire contrasto e riconoscibilità tra background e foreground, tra link visitati e non e tra testo e link, è stato usato: https://contrastchecker.com/

 	- tra link e testo è stato deciso di sottolineare i link e non usare un contrasto colore perché diventava complicato e anche piuttosto brutto visivamente inserire un colore diverso

 	- per i link .btn nonostante le numerose prove con http://colorsafe.co/  e  https://contrastchecker.com/ è stato impossibile trovare un colore che fosse in contrasto >= 4.5, con #16286e (blu notte), e contrasto >=3, con #fffacd (crema), quindi per rispettare i criteri di accessibilità e mantenere una distinzione chiara tra stato visited e non visited, abbiamo scelto di modificare la visualizzazione del bottone nello stato visited andando a cambiare: il colore dello sfondo e del testo, e inserendo un bordo visibile dello stesso colore del testo. In questo modo la differenza è percepibile visivamente e il testo rimane leggibile grazie al contrasto sufficiente con il nuovo sfondo

* per le immagini- le immagini in index ha sneso che abbiano le dimensioni definite nel CSS mentre quando la pagina diventa 
molto complessa perchè contiene molte immagini stile "galleria" è meglio definirle nell'HTML per rendre 
l'esperienza utente migliore in quando ciò garantisce un rendering più veloce


**Index.html**

* inserito <meta name="viewport" content="width=device-width"/> perché fa sì che il css faccia riferimento alla dimensione del device e non alla sua risoluzione --> evita che in un tel ad alta risoluzione venga aperta la visualizzazione desktop

* <aside> per i filtri e non <section>, in quanto rappresentano contenuti accessori rispetto al flusso principale della pagina (l’elenco degli annunci).

* da <dt> e <dd> a <h4> in titolo nelle card --> Il titolo dell’annuncio è stato sostituito da <dt> a <h4> per migliorare la rappresentazione semantica e il ranking. Gli heading riflettono la gerarchia logica dei contenuti (<h2> per la sezione, <h3> per la categoria, <h4> per il singolo annuncio), consentendo ai motori di ricerca e agli screen reader di interpretare correttamente la struttura informativa e di attribuire maggiore rilevanza ai titoli degli annunci.



**Esplora.html**

* da <dt> e <dd> a <h4> in titolo nelle card --> come in index.html


- contrasto tra bottonone bg e freccia (https://codepen.io/yaphi1/pen/oNbEqGV) opacità testa e img test in relazione/images/
- scelta di carosello con blocco iniziale e finale e non loop
- classe aiuti