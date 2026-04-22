-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 22, 2026 alle 20:58
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestionale`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `clienti`
--

CREATE TABLE `clienti` (
  `ID_Cliente` int(11) NOT NULL,
  `Nome_Azienda` varchar(255) DEFAULT NULL,
  `Indirizzo` varchar(255) DEFAULT NULL,
  `P_IVA` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `clienti`
--

INSERT INTO `clienti` (`ID_Cliente`, `Nome_Azienda`, `Indirizzo`, `P_IVA`, `Email`) VALUES
(1, 'Jesus of sorbetto', 'Via Campisio 12', '01234560123 ', 'valepicci2000@gmail.com'),
(4, 'val3p 96', 'Via campisio 12 b', '0124560124', 'cppiplay@gmail.com');

-- --------------------------------------------------------

--
-- Struttura della tabella `dipendenti`
--

CREATE TABLE `dipendenti` (
  `ID_Dipendente` int(11) NOT NULL,
  `ID_Filiale` int(11) DEFAULT NULL,
  `Username` varchar(100) DEFAULT NULL,
  `Pswd` text DEFAULT NULL,
  `Nome` varchar(100) DEFAULT NULL,
  `Cognome` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Data_Assunzione` date DEFAULT NULL,
  `Stipendio` decimal(10,2) DEFAULT NULL,
  `IBAN` varchar(34) DEFAULT NULL,
  `Tipo` varchar(50) DEFAULT NULL,
  `Is_admin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `dipendenti`
--

INSERT INTO `dipendenti` (`ID_Dipendente`, `ID_Filiale`, `Username`, `Pswd`, `Nome`, `Cognome`, `Email`, `Data_Assunzione`, `Stipendio`, `IBAN`, `Tipo`, `Is_admin`) VALUES
(1, 1, 'croix89', 'b133a0c0e9bee3be20163d2ad31d6248db292aa6dcb1ee087a2aa50e0fc75ae2', 'Marco', 'Merrino', 'Marcomerrino89@gmail.com', '2026-02-26', 2345.00, 'IBAN', 'Boss', 1),
(2, 1, 'ValePicciuz', 'b133a0c0e9bee3be20163d2ad31d6248db292aa6dcb1ee087a2aa50e0fc75ae2', 'valerio', 'piccinini', 'piccinini.valerio@einaudicorreggio.it', '2026-02-27', 1.00, 'BELLAZI', 'schiavo', 0),
(3, 1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'admin', 'admin', 'valepicci2000@gmail.com', '2026-04-16', 2000.00, 'vbfhebiwd', 'amministratore', 1),
(5, 1, 'jesu', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Jesus', 'sorbetto', 'davide.campani@einaudicorreggio.it', '2026-04-19', 1899.00, 'jesu', 'dio', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `fatture`
--

CREATE TABLE `fatture` (
  `ID_Fattura` int(11) NOT NULL,
  `ID_Ordine` int(11) NOT NULL,
  `Data_Emissione` date NOT NULL,
  `Importo_Totale` decimal(10,2) NOT NULL,
  `Stato_Pagamento` enum('In attesa','Pagato','Annullato') DEFAULT 'In attesa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `filiali`
--

CREATE TABLE `filiali` (
  `id_filiale` int(11) NOT NULL,
  `Indirizzo` varchar(255) DEFAULT NULL,
  `Tipo` enum('prod','vendita','misto') DEFAULT NULL,
  `Recapito_Telefonico` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `filiali`
--

INSERT INTO `filiali` (`id_filiale`, `Indirizzo`, `Tipo`, `Recapito_Telefonico`) VALUES
(1, 'Via Brombeis 17', '', '3482559999');

-- --------------------------------------------------------

--
-- Struttura della tabella `magazzini`
--

CREATE TABLE `magazzini` (
  `ID_Magazzino` int(11) NOT NULL,
  `Indirizzo` varchar(255) DEFAULT NULL,
  `Desc_Magazzino` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `magazzini`
--

INSERT INTO `magazzini` (`ID_Magazzino`, `Indirizzo`, `Desc_Magazzino`) VALUES
(1, 'Via dai coglioni 2', 'Molto grande');

-- --------------------------------------------------------

--
-- Struttura della tabella `magazzini_prodotti`
--

CREATE TABLE `magazzini_prodotti` (
  `id_prodotto` int(11) NOT NULL,
  `id_magazzino` int(11) NOT NULL,
  `quantita` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `magazzini_prodotti`
--

INSERT INTO `magazzini_prodotti` (`id_prodotto`, `id_magazzino`, `quantita`) VALUES
(1, 1, 40);

-- --------------------------------------------------------

--
-- Struttura della tabella `ordini`
--

CREATE TABLE `ordini` (
  `ID_Ordine` int(11) NOT NULL,
  `ID_Cliente` int(11) DEFAULT NULL,
  `ID_Dipendente` int(11) DEFAULT NULL,
  `Data_Ordine` date DEFAULT NULL,
  `Data_Arrivo` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `ordini`
--

INSERT INTO `ordini` (`ID_Ordine`, `ID_Cliente`, `ID_Dipendente`, `Data_Ordine`, `Data_Arrivo`) VALUES
(1, 1, 2, '2026-04-21', '2026-04-23'),
(2, 4, 1, '2026-04-21', '2026-04-25'),
(7, 1, 3, '2026-04-22', '2026-04-23'),
(8, 1, 3, '2026-04-22', '2026-04-24');

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotti`
--

CREATE TABLE `prodotti` (
  `ID_Prodotto` int(11) NOT NULL,
  `Nome` varchar(255) DEFAULT NULL,
  `Descrizione` varchar(255) NOT NULL,
  `Prezzo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `prodotti`
--

INSERT INTO `prodotti` (`ID_Prodotto`, `Nome`, `Descrizione`, `Prezzo`) VALUES
(1, 'Macchina potatrice', '', 7500.00),
(3, 'hair drier', 'fono crazy 10/10', 67.76);

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotti_ordine`
--

CREATE TABLE `prodotti_ordine` (
  `id_ordine` int(11) NOT NULL,
  `id_prodotto` int(11) NOT NULL,
  `id_magazzino` int(11) NOT NULL,
  `quantita` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `prodotti_ordine`
--

INSERT INTO `prodotti_ordine` (`id_ordine`, `id_prodotto`, `id_magazzino`, `quantita`) VALUES
(8, 1, 1, 10);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `clienti`
--
ALTER TABLE `clienti`
  ADD PRIMARY KEY (`ID_Cliente`),
  ADD UNIQUE KEY `P_IVA` (`P_IVA`);

--
-- Indici per le tabelle `dipendenti`
--
ALTER TABLE `dipendenti`
  ADD PRIMARY KEY (`ID_Dipendente`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `ID_Filiale` (`ID_Filiale`);

--
-- Indici per le tabelle `fatture`
--
ALTER TABLE `fatture`
  ADD PRIMARY KEY (`ID_Fattura`),
  ADD UNIQUE KEY `unique_ordine` (`ID_Ordine`);

--
-- Indici per le tabelle `filiali`
--
ALTER TABLE `filiali`
  ADD PRIMARY KEY (`id_filiale`);

--
-- Indici per le tabelle `magazzini`
--
ALTER TABLE `magazzini`
  ADD PRIMARY KEY (`ID_Magazzino`);

--
-- Indici per le tabelle `magazzini_prodotti`
--
ALTER TABLE `magazzini_prodotti`
  ADD PRIMARY KEY (`id_prodotto`,`id_magazzino`),
  ADD KEY `fk_magazzino_rel` (`id_magazzino`);

--
-- Indici per le tabelle `ordini`
--
ALTER TABLE `ordini`
  ADD PRIMARY KEY (`ID_Ordine`),
  ADD KEY `ID_Cliente` (`ID_Cliente`),
  ADD KEY `ID_Dipendente` (`ID_Dipendente`);

--
-- Indici per le tabelle `prodotti`
--
ALTER TABLE `prodotti`
  ADD PRIMARY KEY (`ID_Prodotto`);

--
-- Indici per le tabelle `prodotti_ordine`
--
ALTER TABLE `prodotti_ordine`
  ADD PRIMARY KEY (`id_ordine`,`id_prodotto`,`id_magazzino`),
  ADD KEY `fk_po_prodotto` (`id_prodotto`),
  ADD KEY `fk_po_magazzino` (`id_magazzino`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `clienti`
--
ALTER TABLE `clienti`
  MODIFY `ID_Cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `dipendenti`
--
ALTER TABLE `dipendenti`
  MODIFY `ID_Dipendente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `fatture`
--
ALTER TABLE `fatture`
  MODIFY `ID_Fattura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `filiali`
--
ALTER TABLE `filiali`
  MODIFY `id_filiale` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `magazzini`
--
ALTER TABLE `magazzini`
  MODIFY `ID_Magazzino` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `ordini`
--
ALTER TABLE `ordini`
  MODIFY `ID_Ordine` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `prodotti`
--
ALTER TABLE `prodotti`
  MODIFY `ID_Prodotto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `dipendenti`
--
ALTER TABLE `dipendenti`
  ADD CONSTRAINT `dipendenti_ibfk_1` FOREIGN KEY (`ID_Filiale`) REFERENCES `filiali` (`id_filiale`);

--
-- Limiti per la tabella `fatture`
--
ALTER TABLE `fatture`
  ADD CONSTRAINT `fk_fattura_ordine` FOREIGN KEY (`ID_Ordine`) REFERENCES `ordini` (`ID_Ordine`) ON DELETE CASCADE;

--
-- Limiti per la tabella `magazzini_prodotti`
--
ALTER TABLE `magazzini_prodotti`
  ADD CONSTRAINT `fk_magazzino_rel` FOREIGN KEY (`id_magazzino`) REFERENCES `magazzini` (`ID_Magazzino`),
  ADD CONSTRAINT `fk_prodotto_rel` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`ID_Prodotto`);

--
-- Limiti per la tabella `ordini`
--
ALTER TABLE `ordini`
  ADD CONSTRAINT `ordini_ibfk_1` FOREIGN KEY (`ID_Cliente`) REFERENCES `clienti` (`ID_Cliente`),
  ADD CONSTRAINT `ordini_ibfk_2` FOREIGN KEY (`ID_Dipendente`) REFERENCES `dipendenti` (`ID_Dipendente`);

--
-- Limiti per la tabella `prodotti_ordine`
--
ALTER TABLE `prodotti_ordine`
  ADD CONSTRAINT `fk_po_magazzino` FOREIGN KEY (`id_magazzino`) REFERENCES `magazzini` (`ID_Magazzino`),
  ADD CONSTRAINT `fk_po_ordine` FOREIGN KEY (`id_ordine`) REFERENCES `ordini` (`ID_Ordine`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_po_prodotto` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`ID_Prodotto`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
