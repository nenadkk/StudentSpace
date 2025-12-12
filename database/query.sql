-- query popolazione card esplora
SELECT 
    a.IdAnnuncio, -- per href="annuncio.html?id=IdAnnuncio" in iù dettagli
    a.Titolo,
    DATE_FORMAT(a.DataPubblicazione, '%d/%m/%Y') AS DataPubblicazione,
    ci.NomeCitta,
    c.NomeCategoria,
    i.Percorso AS Immagine,
    i.AltText
FROM Annuncio a
JOIN Citta ci ON a.IdCitta = ci.IdCitta
JOIN Categoria c ON a.IdCategoria = c.IdCategoria
LEFT JOIN ImmaginiAnnuncio i 
       ON a.IdAnnuncio = i.IdAnnuncio 
      AND i.Ordine = 1   -- immagine principale
ORDER BY a.DataPubblicazione DESC;

<li class="carta">
  <img src="[Immagine]" alt="[AltText]" width="250" height="150">
  <h3>{[Titolo]}</h3>
  <dl>
    <dt>Data pubblicazione</dt>
    <dd>[DataPubblicazione]</dd>
    <dt>Città</dt>
    <dd>[NomeCitta]</dd>
    <dt>Categoria</dt>
    <dd>
      <span class="etichettaCategoria" data-categoria="[NomeCategoria | lowercase]" aria-label="Categoria: [NomeCategoria]">
        [NomeCategoria]
      </span>
    </dd>
  </dl>
  <a class="dettagli" href="annuncio.html?id=[idAnnuncio]" aria-label="Più dettagli sull'annuncio: [Titolo]">
    <span>Più dettagli</span>
  </a>
</li>


-- filtraggio esplora per categoria
SELECT 
    a.IdAnnuncio,
    a.Titolo,
    DATE_FORMAT(a.DataPubblicazione, '%d/%m/%Y') AS DataPubblicazione,
    ci.NomeCitta,
    c.NomeCategoria,
    i.Percorso AS Immagine,
    i.AltText
FROM Annuncio a
JOIN Citta ci ON a.IdCitta = ci.IdCitta
JOIN Categoria c ON a.IdCategoria = c.IdCategoria
LEFT JOIN ImmaginiAnnuncio i 
       ON a.IdAnnuncio = i.IdAnnuncio 
      AND i.Ordine = 1
WHERE c.NomeCategoria = ?  -- parametro preso dall’URL explora.php?categoria=[idCategoria] 
ORDER BY a.DataPubblicazione DESC;

-- con in PHP 
-- $categoria = $_GET['categoria'];
-- $sql = "SELECT ... WHERE a.IdCategoria = ?";

-- query ultimi inseriti
SELECT 
    a.IdAnnuncio,
    a.Titolo,
    DATE_FORMAT(a.DataPubblicazione, '%d/%m/%Y') AS DataPubblicazione,
    ci.NomeCitta,
    c.NomeCategoria,
    i.Percorso AS Immagine,
    i.AltText
FROM Annuncio a
JOIN Citta ci ON a.IdCitta = ci.IdCitta
JOIN Categoria c ON a.IdCategoria = c.IdCategoria
LEFT JOIN ImmaginiAnnuncio i 
       ON a.IdAnnuncio = i.IdAnnuncio 
      AND i.Ordine = 1   
ORDER BY a.DataPubblicazione DESC
LIMIT n;   -- ultimi n annunci -> decidere quanti

