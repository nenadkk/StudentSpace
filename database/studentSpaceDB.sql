 USE testdb;

-- ============================
-- DROP TABLES (ordine corretto)
-- ============================
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
    PrezzoOrario DECIMAL(3,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    	FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        	ON DELETE CASCADE ON UPDATE CASCADE
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

INSERT INTO Utente (Nome, Cognome, Email, Password, IdCitta) VALUES
('Mario', 'Rossi', 'mario.rossi@example.com', 'password123', 1);

INSERT INTO Annuncio (Titolo, Descrizione, Categoria, IdUtente, IdCitta) VALUES
('Appartamento in affitto a Milano', 'Bellissimo appartamento di 2 camere in centro a Milano.', 'Affitti', 1, 1),
('Ripetizioni di Matematica', 'Offro ripetizioni di matematica per studenti delle superiori.', 'Ripetizioni', 1, 23);

INSERT INTO ImmaginiAnnuncio (IdAnnuncio, Percorso, AltText, Decorativa, Ordine) VALUES
--(1, 'bilocaleCentro.jpg', "Foto dell\'appartamento in affitto a Milano", 0, 1),
(1, 'stanzaSingola.jpg', '', 1, 1),
(2, 'ripetizioniMate.jpg', 'Foto per ripetizioni di matematica', 0, 1);

