 USE testdb;

-- ============================
-- DROP TABLES (ordine corretto)
-- ============================
DROP TABLE IF EXISTS Preferiti;
DROP TABLE IF EXISTS AnnuncioRipetizioni;
DROP TABLE IF EXISTS AnnuncioEventi;
DROP TABLE IF EXISTS AnnuncioEsperimenti;
DROP TABLE IF EXISTS AnnuncioAffitti;
DROP TABLE IF EXISTS ImmaginiAnnuncio;
DROP TABLE IF EXISTS Annuncio;
DROP TABLE IF EXISTS Utente;
-- DROP TABLE IF EXISTS Categoria;
DROP TABLE IF EXISTS Citta;

-- -----------------------------------------------------
-- CREATE
-- -----------------------------------------------------

CREATE TABLE Citta (
    IdCitta 	INT AUTO_INCREMENT PRIMARY KEY,
    NomeCitta 	VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

-- CREATE TABLE Categoria (
--    IdCategoria 	INT AUTO_INCREMENT PRIMARY KEY,
--    NomeCategoria 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL UNIQUE
-- ) ENGINE=InnoDB
-- DEFAULT CHARSET=utf8
-- COLLATE=utf8_unicode_ci;

CREATE TABLE Utente (
    IdUtente 	INT AUTO_INCREMENT PRIMARY KEY,
    Nome 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    Cognome	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    Email 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL UNIQUE,
    Password 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL, -- minimo 8 caratteri
    IdCitta 	INT, -- non required
   	 FOREIGN KEY (IdCitta) REFERENCES Citta(IdCitta)
       	 	ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

CREATE TABLE Annuncio (
    IdAnnuncio 	INT AUTO_INCREMENT PRIMARY KEY,
    Titolo 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    Descrizione LONGTEXT COLLATE utf8_unicode_ci,
    DataPubblicazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    Categoria ENUM('Affitti', 'Esperimenti', 'Eventi', 'Ripetizioni'),
    IdUtente 	INT NOT NULL,
    -- IdCategoria INT NOT NULL,
    IdCitta 	INT NOT NULL,
    	FOREIGN KEY (IdUtente) REFERENCES Utente(IdUtente)
        	ON DELETE RESTRICT ON UPDATE CASCADE,
    --	FOREIGN KEY (IdCategoria) REFERENCES Categoria(IdCategoria)
    --    	ON DELETE RESTRICT ON UPDATE CASCADE,
    	FOREIGN KEY (IdCitta) REFERENCES Citta(IdCitta)
        	ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

CREATE TABLE ImmaginiAnnuncio (
    IdImmagine 	INT AUTO_INCREMENT PRIMARY KEY,
    IdAnnuncio 	INT NOT NULL,
    Percorso 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    AltText 	LONGTEXT COLLATE utf8_unicode_ci,
    Decorativa 	TINYINT(1) DEFAULT 0,
    Ordine 	INT NOT NULL,
    	FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

CREATE TABLE AnnuncioAffitti (
    IdAnnuncio 	INT PRIMARY KEY,
    PrezzoMensile DECIMAL(7,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    Indirizzo 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    NumeroInquilini INT NOT NULL,
    	FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

CREATE TABLE AnnuncioEsperimenti (
    IdAnnuncio 	INT PRIMARY KEY,
    Laboratorio VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    DurataPrevista INT COLLATE utf8_unicode_ci NOT NULL,
    Compenso DECIMAL(7,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    	FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;


CREATE TABLE AnnuncioEventi (
    IdAnnuncio 	INT PRIMARY KEY,
    DataEvento 	DATE NOT NULL,
    Luogo VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    CostoEntrata DECIMAL(7,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    	FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;


CREATE TABLE AnnuncioRipetizioni (
    IdAnnuncio 	INT PRIMARY KEY,
    Materia 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    Livello 	VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    PrezzoOrario DECIMAL(4,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    	FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        	ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

CREATE TABLE Preferiti (
    IdAnnuncio INT,
    IdUtente INT,
    FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (IdUtente) REFERENCES Utente(IdUtente)
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE(IdAnnuncio, IdUtente)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci;

-- -----------------------------------------------------
-- INSERT
-- -----------------------------------------------------

INSERT INTO Citta (NomeCitta) VALUES
('Ancona'),
('Aosta'),
('Bari'),
('Benevento'),
('Bergamo'),
('Bologna'),
('Bolzano'),
('Brescia'),
('Cagliari'),
('Caserta'),
('Catania'),
('Catanzaro'),
('Chieti'),
('Cosenza'),
('Ferrara'),
('Firenze'),
('Foggia'),
('Frosinone'),
('Genova'),
("L'Aquila"),
('Lecce'),
('Lucca'),
('Milano'),
('Modena'),
('Messina'),
('Napoli'),
('Padova'),
('Palermo'),
('Parma'),
('Pavia'),
('Perugia'),
('Pisa'),
('Potenza'),
('Reggio Calabria'),
('Roma'),
('Sassari'),
('Siena'),
('Teramo'),
('Torino'),
('Trento'),
('Trieste'),
('Udine'),
('Urbino'),
('Varese'),
('Vercelli'),
('Venezia'),
('Verona'),
('Viterbo');

-- =====================================================
-- UTENTI
-- =====================================================

INSERT INTO Utente (Nome, Cognome, Email, Password, IdCitta) VALUES
('ff', 'ff', 'ff@ff.ff',
 '$2y$10$ICFWdfUEcDo.i9NFDjedT.unJxpq59W5UzaZFBgacSyLOn7Y119ja',
 32),
('Marco', 'Rossi', 'marco.rossi@email.it',
 '$2y$10$EIXCh8J2r6uFj3X3bEw1ReC3h3bYxX6p6j6CqzU8N9KZr4z5Rr7bS',
 35),
('Giulia', 'Bianchi', 'giulia.bianchi@email.it',
 '$2y$10$KIXe3D9f3J7mT1A6b2XnHOP8P4ZCzYxJk6qU5r7T1Z9Ew8yR4P3QW',
 23),
('Luca', 'Verdi', 'luca.verdi@email.it',
 '$2y$10$VIXp0Y8U9A1mFQ2sK8E7b3JZrL5X6mT4C9D2H1WQeR6N0yB5Z8S',
 6);

-- =====================================================
-- ANNUNCI (20)
-- =====================================================

INSERT INTO Annuncio (Titolo, Descrizione, Categoria, IdUtente, IdCitta) VALUES
-- AFFITTI (1–5)
('Stanza singola per studenti', 'Stanza luminosa in appartamento condiviso.', 'Affitti', 2, 23),
('Bilocale arredato vicino metro', 'Bilocale arredato, ideale per coppie.', 'Affitti', 2, 35),
('Posto letto in doppia', 'Posto letto in camera doppia.', 'Affitti', 3, 26),
('Monolocale ristrutturato', 'Monolocale ristrutturato di recente.', 'Affitti', 3, 16),
('Appartamento studenti centro', 'Appartamento per 4 studenti.', 'Affitti', 1, 32),

-- RIPETIZIONI (6–10)
('Ripetizioni di Fisica', 'Fisica per licei scientifici.', 'Ripetizioni', 1, 23),
('Lezioni di Inglese', 'Inglese livello B1–C1.', 'Ripetizioni', 3, 35),
('Ripetizioni di Programmazione C', 'Supporto per esami universitari.', 'Ripetizioni', 4, 6),
('Ripetizioni di Latino', 'Latino per liceo classico.', 'Ripetizioni', 3, 16),
('Ripetizioni di Economia', 'Economia aziendale e politica.', 'Ripetizioni', 1, 32),

-- EVENTI (11–15)
('Concerto jazz dal vivo', 'Concerto jazz con band locale.', 'Eventi', 2, 35),
('Workshop di fotografia', 'Fotografia urbana pratica.', 'Eventi', 3, 23),
('Seminario di cybersecurity', 'Introduzione alla sicurezza informatica.', 'Eventi', 4, 6),
('Fiera del fumetto', 'Evento dedicato a fumetti e cosplay.', 'Eventi', 1, 47),
('Corso di cucina italiana', 'Cucina tradizionale italiana.', 'Eventi', 2, 26),

-- ESPERIMENTI (16–20)
('Studio sulla memoria', 'Studio su memoria a breve termine.', 'Esperimenti', 1, 23),
('Test di usabilità app', 'Test UX su app mobile.', 'Esperimenti', 2, 35),
('Studio sul sonno', 'Ricerca su ciclo del sonno.', 'Esperimenti', 4, 6),
('Esperimento realtà virtuale', 'Interazione in ambienti VR.', 'Esperimenti', 1, 32),
('Ricerca sullo stress', 'Studio psicologico sullo stress.', 'Esperimenti', 3, 19);

-- =====================================================
-- TABELLE SPECIALIZZATE
-- =====================================================

INSERT INTO AnnuncioAffitti VALUES
(1,280.00,'Via Leopardi 12',3),
(2,750.00,'Via Roma 45',2),
(3,200.00,'Via Toledo 88',2),
(4,500.00,'Via Verdi 9',1),
(5,900.00,'Via Garibaldi 21',4);

INSERT INTO AnnuncioRipetizioni VALUES
(6,'Fisica','Superiori',15.00),
(7,'Inglese','Medie',18.00),
(8,'Programmazione C','Università',20.00),
(9,'Latino','Superiori',14.00),
(10,'Economia','Università',22.00);

INSERT INTO AnnuncioEventi VALUES
(11,'2025-06-10','Teatro Comunale',12.00),
(12,'2025-05-22','Centro culturale',25.00),
(13,'2025-07-01','Aula Magna Università',0.00),
(14,'2025-08-15','Fiera cittadina',8.00),
(15,'2025-06-30','Scuola di cucina',40.00);

INSERT INTO AnnuncioEsperimenti VALUES
(16,'Laboratorio di Psicologia',60,10.00),
(17,'UX Lab',45,15.00),
(18,'Centro del Sonno',90,25.00),
(19,'VR Lab',30,12.00),
(20,'Dipartimento Psicologia',75,20.00);

-- =====================================================
-- IMMAGINI ANNUNCI
-- =====================================================

INSERT INTO ImmaginiAnnuncio (IdAnnuncio, Percorso, AltText, Decorativa, Ordine) VALUES
-- AFFITTI
(1,'affitti1_1.jpg','Camera singola luminosa con parete gialla, letto bianco, scrivania con sedia da ufficio e parquet a spina di pesce..',0,1),
(1,'affitti1_2.jpg',"Ingresso luminoso con accesso a due camere: una con parete gialla e piastrelle, l’altra, presente nell'immagine precedente con parquet e finestra. Corridoio con specchio a parete e pavimento decorato.",1,2),

(2,'affitti2_1.jpg','Scrivania in legno con sedia, davanti a una grande finestra con vista su edifici e alberi. Ambiente luminoso e minimalista, adatto allo studio o al lavoro.',0,1),
(2,'affitti2_2.jpg','Soggiorno moderno con divano blu, cuscini gialli, tappeto giallo e tavolino bianco. Porta finestra con tende bianche e accesso al balcone. TV con mensole e decorazioni.',1,2),
(2,'affitti2_3.jpg','Cucina moderna con mobili in legno chiaro, forno, piano cottura e frigorifero. Tavolo con quattro sedie gialle in plastica. Pavimento piastrellato e finestra con tende leggere.',1,3),
(2,'affitti2_4.jpg','Bagno moderno con doccia angolare a vetro, lavabo con specchio, WC e bidet. Piastrelle verdi e bianche, illuminazione sopra lo specchio.',1,4),

(3,'affitti3_1.jpg','Camera con due letti singoli affiancati, comodino centrale con lampada, armadio con ante a specchio e finestra con tapparella. Sopra il comodino quadro marino e climatizzatore.',0,1),
(3,'affitti3_2.jpg','Camera con due letti singoli paralleli, comodino centrale con lampada, cassettiera con TV e quadro marino. Finestra con tapparella e climatizzatore.',1,2),

(4,'affitti4_1.jpg','Camera con letto matrimoniale, sedia maculata, tavolino e scaffale metallico con oggetti vintage. Pavimento grigio con tappeto intrecciato, cassapanca con pianta e decorazione appesa.',0,1),
(4,'affitti4_2.jpg','Camera con letto singolo, comodino, scaffale con libri e decorazioni, cassettiera con pianta. Pavimento chiaro, tappeto e cuscino a terra. Porta a vetro sul lato destro.',1,2),
(4,'affitti4_3.jpg','Monolocale con letto, tavolo da pranzo in legno, sedia con coperta e scaffale con libri, piante e microonde. ',1,3),
(4,'affitti4_4.jpg','Bagno compatto con doccia a vetro, lavabo con specchio, WC e lavatrice, mobile in legno e pavimento effetto legno.',1,4),

(5,'affitti5_1.jpg','Cucina moderna con mobili bianchi e azzurri, tavolo in legno con quattro sedie e lampade a sospensione. Finestra e luce naturale, tappeto decorativo vicino alla porta.',0,1),
(5,'affitti5_2.jpg','Camera singola con letto bianco e grigio, comodino con lampada, scrivania con sedia e pianta.',1,2),
(5,'affitti5_3.jpg','Camera con due letti singoli paralleli, comodino centrale, pavimento piastrellato chiaro e pareti bianche.',1,3),
(5,'affitti5_4.jpg','Bagno moderno con doccia a parete, WC sospeso, lavabo con specchio illuminato e scaldabagno sopra il WC.',1,4),

-- ESPERIMENTI
(16,'esperimenti1_1.jpg','',1,1),
(17,'esperimenti2_1.jpg','',1,1),
(18,'esperimenti3_1.jpg','',1,1),
(19,'esperimenti4_1.jpg','',1,1),
(20,'esperimenti5_1.jpg','',1,1),

-- EVENTI
(11,'eventi1_1.jpg','Primo piano di persona con sax, sullo sfondo festival con tende, luci e persone.',0,1),
(11,'eventi1_2.jpg','',1,2),

(12,'eventi2_1.jpg','Persona con macchina fotografica accanto a grande fotocamera e luci da studio. ',0,1),
(12,'eventi2_2.jpg','Due persone in studio osservano una fotocamera DSLR; una indica lo schermo, l’altra la tiene.',0,2),
(12,'eventi2_3.jpg','Due persone in studio esaminano fotocamere: una mostra lo schermo, l’altra tiene una fotocamera compatta.',0,3),

(13,'eventi3_1.jpg','',1,1),

(14,'eventi4_1.jpg','Persone leggono o usano dispositivi tra pile di libri colorati disposte a piramide.',0,1),
(14,'eventi4_2.jpg','Tavolo pieno di manga con persone che sfogliano e acquistano.',0,2),

(15,'eventi5_1.jpg','Mano che usa macchina per pasta su piano infarinato, con uova, farina, olio e tagliatelle.',0,1),

-- RIPETIZIONI
(6,'ripetizioni1_1.jpg','',1,1),
(7,'ripetizioni2_1.jpg','',1,1),
(8,'ripetizioni3_1.jpg','',1,1),
(9,'ripetizioni4_1.jpg','',1,1),
(10,'ripetizioni5_1.jpg','',1,1);
