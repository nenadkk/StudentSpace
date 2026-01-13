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

* in css in has() --> su can i use: Newly available across major browsers

* con js disabilitato comunque i messaggi di errore hanno role allert quindi vengono elencati appena si carica la pagina post submit

* sono stati selezionati i type (es.search, email, password) appropriati per gli input in html per migliorare l'usabilità e l'esperienza utente ma ovviamente tutto è validato oltre che nell'immediato via jave che lato server --> Usare i controlli HTML5 per la validazione base e aggiungere solo il minimo JS per messaggi chiari e regole di business, mantenendo sempre la validazione lato server.

\section{Validazione JavaScript con gestione del TAB attiva solo su alcuni form}

Nel progetto è stato implementato un sistema di validazione client-side avanzato, progettato per migliorare l’accessibilità e l’esperienza utente nei form più complessi (registrazione, pubblicazione e modifica annuncio). 
L’obiettivo principale è garantire un flusso di compilazione chiaro e accessibile, senza interferire con i form più semplici come \textit{accedi} o \textit{filtri}.

\subsection{Attivazione selettiva tramite attributo \texttt{data-validate}}

Non tutti i form richiedono la stessa complessità di controlli. 
Per questo motivo è stato introdotto un attributo HTML personalizzato:

\begin{verbatim}
<form data-validate="registrazione">
\end{verbatim}

I form che includono l’attributo \texttt{data-validate} attivano la validazione JavaScript, mentre gli altri vengono ignorati.  
Questo approccio evita che la validazione venga applicata in contesti dove non è necessaria.

\paragraph{Form con validazione attiva}
\begin{itemize}
    \item Registrazione
    \item Pubblica annuncio
    \item Modifica annuncio
\end{itemize}

\paragraph{Form senza validazione JS}
\begin{itemize}
    \item Accedi
    \item Filtri
\end{itemize}

\subsection{Inizializzazione condizionata dello script}

All’avvio, lo script verifica se la pagina contiene un form con \texttt{data-validate}.  
Se non lo trova, la validazione viene completamente disattivata:

\begin{verbatim}
const form = document.querySelector("form[data-validate]");
if (!form) return;
\end{verbatim}

Questo garantisce che:
\begin{itemize}
    \item il codice non venga eseguito inutilmente,
    \item non vengano generati errori nei form semplici,
    \item la pagina \textit{accedi} rimanga pulita e con messaggi server-side controllati.
\end{itemize}

\subsection{Validazione immediata e gestione del TAB}

Nei form complessi, la validazione è stata progettata per essere immediata e accessibile.  
Quando l’utente preme TAB su un campo:

\begin{enumerate}
    \item il campo viene validato,
    \item se valido, il TAB procede normalmente,
    \item se non valido, il TAB viene bloccato e il focus viene spostato sul messaggio di errore.
\end{enumerate}

Il messaggio di errore è reso focusabile tramite \texttt{tabindex="0"}, così da essere annunciato correttamente dai lettori di schermo.

Premendo nuovamente TAB sul messaggio di errore, il focus ritorna automaticamente al campo da correggere, creando un ciclo controllato:



\[
\text{campo} \rightarrow \text{errore} \rightarrow \text{campo}
\]



\subsection{Inserimento degli errori sotto al campo}

Gli errori vengono inseriti immediatamente dopo il campo, migliorando la leggibilità:

\begin{verbatim}
campo.insertAdjacentElement("afterend", ul);
\end{verbatim}

La posizione visiva non influisce sulla logica di focus, che rimane invariata.

\subsection{Motivazioni progettuali}

La validazione JavaScript è stata limitata ai soli campi critici:
\begin{itemize}
    \item nome, cognome, email, password (registrazione),
    \item titolo, città, descrizione (pubblica/modifica).
\end{itemize}

I campi specifici delle categorie (Affitti, Esperimenti, Eventi, Ripetizioni) non richiedono validazione JS perché:
\begin{itemize}
    \item sono opzionali,
    \item vengono abilitati solo dopo la scelta della categoria,
    \item sono già validati lato server,
    \item non richiedono focus management avanzato.
\end{itemize}

\subsection{Benefici della soluzione}

\begin{itemize}
    \item \textbf{Accessibilità migliorata}: navigazione da tastiera fluida e annunci corretti dei messaggi di errore.
    \item \textbf{Esperienza utente chiara}: l’utente non può ignorare un errore senza correggerlo.
    \item \textbf{Modularità}: la validazione si attiva solo dove serve, senza duplicare codice.
    \item \textbf{Manutenibilità}: aggiungere un nuovo form validabile richiede solo l’attributo \texttt{data-validate}.
\end{itemize}

\subsection{Conclusione}

La validazione JavaScript implementata è selettiva, accessibile e modulare.  
Si integra perfettamente con la validazione lato server e garantisce un’esperienza utente coerente e inclusiva nei form più complessi, senza introdurre complessità superflue nei form semplici.



- contrasto tra bottonone bg e freccia (https://codepen.io/yaphi1/pen/oNbEqGV) opacità testa e img test in relazione/images/
- scelta di carosello con blocco iniziale e finale e non loop
- classe aiuti