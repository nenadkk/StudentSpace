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

* in registrati gli alt hanno maxlength="100" per garantire che ci sia un'ottimizzazione delle tempistiche per la conclusione di una task da parte di una persona con disabilità visiva

* in css in has() --> su can i use: Newly available across major browsers

* con js disabilitato comunque i messaggi di errore hanno role allert quindi vengono elencati appena si carica la pagina post submit

* sono stati selezionati i type (es.search, email, password) appropriati per gli input in html per migliorare l'usabilità e l'esperienza utente ma ovviamente tutto è validato oltre che nell'immediato via jave che lato server --> Usare i controlli HTML5 per la validazione base e aggiungere solo il minimo JS per messaggi chiari e regole di business, mantenendo sempre la validazione lato server.

* Nel progetto sono stati utilizzati due approcci diversi per la gestione degli errori globali, in base alla natura del contenuto.

Nella pagina di accesso, il messaggio di errore è inserito dinamicamente all’interno di un contenitore con `aria-live="assertive"`, poiché rappresenta un feedback globale che deve essere annunciato immediatamente ogni volta che il contenuto cambia.

Nella pagina di pubblicazione degli annunci, gli errori relativi alle immagini vengono generati lato server come blocco già completo e statico; per questo motivo viene utilizzato `role="alert"` direttamente sul messaggio, evitando l’uso di contenitori live non necessari.

Le due soluzioni non sono ridondanti ma complementari, e sono state scelte in base al comportamento desiderato e alle linee guida WAI-ARIA.

* \section{Gestione degli errori lato client e lato server}

La validazione del form è stata implementata combinando controlli lato client (JavaScript) e lato server (PHP), mantenendo una struttura uniforme dei messaggi di errore per garantire coerenza visiva e accessibilità.

\subsection{Errori lato client (JavaScript)}
La validazione JavaScript intercetta gli errori durante la compilazione dei campi tramite eventi \texttt{blur} e \texttt{input}, e nuovamente al momento del submit.  
In caso di errore, viene generato dinamicamente un blocco HTML del tipo:

\begin{verbatim}
<ul class="messaggi-errore-form">
    <li class="msgErrore" id="errore-campo" role="alert">...</li>
</ul>
\end{verbatim}

Al campo associato vengono applicati gli attributi ARIA:
\begin{itemize}
    \item \texttt{aria-invalid="true"}
    \item \texttt{aria-describedby="errore-campo"}
\end{itemize}

Per migliorare l'usabilità, il focus viene portato automaticamente sul primo campo non valido, con selezione del contenuto. Questo comportamento avviene \emph{solo alla prima comparsa dell'errore}, evitando di interferire con la navigazione successiva dell'utente.

\subsection{Errori lato server (PHP)}
Gli errori generati dal server dopo l'invio del form vengono inseriti direttamente nell'HTML con la stessa struttura utilizzata da JavaScript, in modo da mantenere uniformità:

\begin{verbatim}
<ul class="messaggi-errore-form">
    <li class="msgErrore" id="errore-campo" role="alert">...</li>
</ul>
\end{verbatim}

Quando la pagina viene ricaricata a seguito di un errore server-side, uno script eseguito al \texttt{DOMContentLoaded} individua il primo messaggio di errore e imposta il focus sul campo corrispondente.  
È stato gestito anche il caso particolare degli errori relativi alle immagini: se l'errore riguarda il caricamento dei file, il focus viene portato automaticamente sul primo campo \texttt{input type="file"}.

\subsection{Coerenza e accessibilità}
L'uso di una struttura HTML identica per gli errori JS e PHP, insieme agli attributi ARIA e alla gestione del focus, garantisce:
\begin{itemize}
    \item coerenza visiva tra validazione client e server;
    \item compatibilità con tecnologie assistive;
    \item una migliore esperienza utente durante la compilazione del form.
\end{itemize}

* \paragraph{Scelta del font e gerarchia tipografica.}
Per l’intero sito è stato adottato il font \textit{Noto Sans}, scelto per la sua
elevata leggibilità, la neutralità stilistica e la coerenza visiva nelle
interfacce web. A differenza di \textit{Inter}, che presenta una minore
distinzione tra alcuni glifi simili (in particolare \texttt{I}, \texttt{l} e
\texttt{1}), \textit{Noto Sans} garantisce una maggiore chiarezza tipografica,
caratteristica particolarmente rilevante nei moduli di inserimento dati e nelle
situazioni in cui la precisione visiva è fondamentale.

La scala dei pesi è stata definita per costruire una gerarchia tipografica
coerente e facilmente percepibile: il peso \textbf{400} è utilizzato per il
testo corrente, il peso \textbf{500} per etichette e componenti dell’interfaccia
utente, il peso \textbf{600} per titoli secondari e sezioni, mentre il peso
\textbf{700} è riservato ai titoli principali. Questa struttura permette di
guidare l’utente nella lettura, migliorare la scansione visiva dei contenuti e
mantenere un equilibrio armonico tra elementi testuali e grafici.

*\paragraph{Predittività delle ancore e supporto tramite \texttt{title}.}
Nel progetto è stato seguito il principio secondo cui ogni ancora deve essere
predittiva rispetto alla destinazione. Nei casi in cui l’interpretazione
visiva potrebbe non essere immediata, come il logo che riporta alla home o
l’icona “torna su”, è stato aggiunto l’attributo \texttt{title} per fornire un
supporto informativo agli utenti vedenti. L’accessibilità è garantita tramite
\texttt{aria-label}, che rimane la fonte principale per i lettori di schermo,
mentre il \texttt{title} svolge un ruolo complementare senza creare
ridondanze.

* Dichiara se hai usato AI per generare testo o immagini -> annunci + illustrazioni

* 2 uniche ancore img:
 1. logo, img da css quindi txt + title per prevedibilità ancora e no aria label per eiatre ridondanza
 2. torna su img con alt esplicativo + title (functional image in https://www.w3.org/WAI/tutorials/images/decision-tree/)

* \subsection*{Ottimizzazione dei titoli e delle meta description} !!!!!!!cambiati

La gestione dei titoli delle pagine (\texttt{<title>}) segue una strategia coerente e uniforme, basata sul formato ``Nome pagina -- Student Space''. Questa scelta garantisce riconoscibilità del sito, coerenza tra le pagine e una buona leggibilità nei risultati dei motori di ricerca. Alcune pagine richiedono tuttavia un titolo più descrittivo, per motivi di chiarezza, usabilità o ottimizzazione SEO. In questi casi il titolo è stato personalizzato includendo informazioni contestuali rilevanti.

I titoli personalizzati sono i seguenti:
\begin{itemize}
    \item \emph{Errore 500: errore interno -- Student Space}
    \item \emph{Errore 404: pagina non trovata -- Student Space}
    \item \emph{Profilo: [Email] -- Student Space}
    \item \emph{Modifica annuncio: [TitoloAnnuncio] -- Student Space}
    \item \emph{[TitoloAnnuncio] -- [CategoriaAnnuncio] a [CittaAnnuncio] -- Student Space}
\end{itemize}

Anche le meta description sono state ottimizzate per migliorare la pertinenza delle pagine nei motori di ricerca. Per le pagine informative è stato adottato un formato descrittivo e sintetico, mentre per le pagine dinamiche (come gli annunci) la description è stata costruita utilizzando i dati dell'annuncio, includendo titolo, categoria e città. Questo approccio permette di comunicare immediatamente il contenuto della pagina e migliora la qualità degli snippet mostrati nei risultati di ricerca.

\subsection*{Strategie SEO adottate}

L'intero sito è stato progettato seguendo buone pratiche SEO e di accessibilità:
\begin{itemize}
    \item struttura semantica corretta con uso appropriato di \texttt{<header>}, \texttt{<main>}, \texttt{<nav>}, \texttt{<section>} e \texttt{<footer>};
    \item utilizzo di titoli gerarchici coerenti (\texttt{<h1>}, \texttt{<h2>}, \texttt{<h3>});
    \item meta description specifiche e descrittive per ogni pagina;
    \item immagini ottimizzate in formato \texttt{.webp} con attributi \texttt{alt} significativi;
    \item breadcrumb semantico con \texttt{aria-current} per migliorare navigazione e accessibilità;
    \item link con anchor text descrittivi, evitando formulazioni generiche come ``clicca qui'';
    \item URL coerenti e leggibili;
    \item ottimizzazione delle pagine dinamiche (annunci) tramite title e description costruiti con dati contestuali.
\end{itemize}

Queste scelte garantiscono una buona indicizzazione, una migliore esperienza utente e una struttura del sito chiara e accessibile.

* in design diciamo che abbiamo fatto usare ad utenti esterni di varie fasce e categorie il sito per verificare che il comportamento sia prevedibile in quanto noi essendo i creatori potevamo non renderci conto di elementi non prevedibili e chiari (fare esempi)

* per ogni tipo di metafora della pesca fornire soluzione data dal sito

* tutti gli input nei form non sono figli diretti di form ma hanno tutti filedset e legend in alcuni casi con classe aiuti quindi solo ascoltabili da screen reader + in modifica sotto h2 salta ad annulla per prevedibilità di poter annullare e non dover leggere tutto prima

- contrasto tra bottonone bg e freccia (https://codepen.io/yaphi1/pen/oNbEqGV) opacità testa e img test in relazione/images/
- scelta di carosello con blocco iniziale e finale e non loop
- classe aiuti