CREATE TABLE Utente (
    IdUtente INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(50) NOT NULL,
    Cognome VARCHAR(50) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL, --minimo 8 caratteri
    IdCitta INT, -- non required
    FOREIGN KEY (IdCitta) REFERENCES Citta(IdCitta)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Categoria (
    IdCategoria INT AUTO_INCREMENT PRIMARY KEY,
    NomeCategoria VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Citta (
    IdCitta INT AUTO_INCREMENT PRIMARY KEY,
    NomeCitta VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Annuncio (
    IdAnnuncio INT AUTO_INCREMENT PRIMARY KEY,
    Titolo VARCHAR(150) NOT NULL,
    Descrizione TEXT,
    DataPubblicazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    IdUtente INT NOT NULL,
    IdCategoria INT NOT NULL,
    IdCitta INT NOT NULL,
    FOREIGN KEY (IdUtente) REFERENCES Utente(IdUtente)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (IdCategoria) REFERENCES Categoria(IdCategoria)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (IdCitta) REFERENCES Citta(IdCitta)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Affitti (
    IdAnnuncio INT PRIMARY KEY,
    PrezzoMensile DECIMAL(4,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    Indirizzo VARCHAR(200) NOT NULL,
    NumeroInquilini INT NOT NULL,
    FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Esperimenti (
    IdAnnuncio INT PRIMARY KEY,
    Laboratorio VARCHAR(100) NOT NULL,
    DurataPrevista INT NOT NULL,
    Compenso DECIMAL(4,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Eventi (
    IdAnnuncio INT PRIMARY KEY,
    DataEvento DATE NOT NULL,
    Luogo VARCHAR(150) NOT NULL,
    CostoEntrata DECIMAL(4,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Ripetizioni (
    IdAnnuncio INT PRIMARY KEY,
    Materia VARCHAR(100) NOT NULL,
    Livello VARCHAR(50) NOT NULL,
    PrezzoOrario DECIMAL(3,2) NOT NULL, -- decimale quindi importante controllo con virgola  o punto
    FOREIGN KEY (IdAnnuncio) REFERENCES Annuncio(IdAnnuncio)
        ON DELETE CASCADE ON UPDATE CASCADE
);
