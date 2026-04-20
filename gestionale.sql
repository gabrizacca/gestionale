-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 20, 2026 alle 17:25
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
(1, 'Jesus of sorbetto', 'Via Campisio 12', '01234560123 ', 'valepicci2000@gmail.com');

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
(1, 1, 'croix89', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Marco', 'Merrino', 'Marcomerrino89@gmail.com', '2026-02-26', 2345.00, 'IBAN', 'Boss', 0),
(2, 1, 'ValePicciuz', 'b133a0c0e9bee3be20163d2ad31d6248db292aa6dcb1ee087a2aa50e0fc75ae2', 'valerio', 'piccinini', 'piccinini.valerio@einaudicorreggio.it', '2026-02-27', 1.00, 'BELLAZI', 'schiavo', NULL),
(3, 1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'admin', 'admin', 'valepicci2000@gmail.com', '2026-04-16', 2000.00, 'vbfhebiwd', 'amministratore', 1),
(5, 1, 'jesu', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Jesus', 'sorbetto', 'davide.campani@einaudicorreggio.it', '2026-04-19', 1899.00, 'jesu', 'dio', 0);

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
-- Struttura della tabella `magazzino_prodotti`
--

CREATE TABLE `magazzino_prodotti` (
  `ID_Magazzino` int(11) NOT NULL,
  `ID_Prodotto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `ordini`
--

CREATE TABLE `ordini` (
  `ID_Ordine` int(11) NOT NULL,
  `ID_Cliente` int(11) DEFAULT NULL,
  `ID_Dipendente` int(11) DEFAULT NULL,
  `Data_Ordine` date DEFAULT NULL,
  `Data_Arrivo` date DEFAULT NULL,
  `ID_prodotto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotti`
--

CREATE TABLE `prodotti` (
  `ID_Prodotto` int(11) NOT NULL,
  `Desc_prodotto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `prodotti`
--

INSERT INTO `prodotti` (`ID_Prodotto`, `Desc_prodotto`) VALUES
(1, 'Macchina potatrice'),
(2, 'alice');

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
-- Indici per le tabelle `magazzino_prodotti`
--
ALTER TABLE `magazzino_prodotti`
  ADD PRIMARY KEY (`ID_Magazzino`,`ID_Prodotto`),
  ADD KEY `ID_Prodotto` (`ID_Prodotto`);

--
-- Indici per le tabelle `ordini`
--
ALTER TABLE `ordini`
  ADD PRIMARY KEY (`ID_Ordine`),
  ADD KEY `ID_Cliente` (`ID_Cliente`),
  ADD KEY `ID_Dipendente` (`ID_Dipendente`),
  ADD KEY `fk_ordini_prodotto` (`ID_prodotto`);

--
-- Indici per le tabelle `prodotti`
--
ALTER TABLE `prodotti`
  ADD PRIMARY KEY (`ID_Prodotto`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `clienti`
--
ALTER TABLE `clienti`
  MODIFY `ID_Cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `dipendenti`
--
ALTER TABLE `dipendenti`
  MODIFY `ID_Dipendente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `ID_Ordine` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `prodotti`
--
ALTER TABLE `prodotti`
  MODIFY `ID_Prodotto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `dipendenti`
--
ALTER TABLE `dipendenti`
  ADD CONSTRAINT `dipendenti_ibfk_1` FOREIGN KEY (`ID_Filiale`) REFERENCES `filiali` (`id_filiale`);

--
-- Limiti per la tabella `magazzino_prodotti`
--
ALTER TABLE `magazzino_prodotti`
  ADD CONSTRAINT `magazzino_prodotti_ibfk_1` FOREIGN KEY (`ID_Magazzino`) REFERENCES `magazzini` (`ID_Magazzino`),
  ADD CONSTRAINT `magazzino_prodotti_ibfk_2` FOREIGN KEY (`ID_Prodotto`) REFERENCES `prodotti` (`ID_Prodotto`);

--
-- Limiti per la tabella `ordini`
--
ALTER TABLE `ordini`
  ADD CONSTRAINT `fk_ordini_prodotto` FOREIGN KEY (`ID_prodotto`) REFERENCES `prodotti` (`ID_Prodotto`),
  ADD CONSTRAINT `ordini_ibfk_1` FOREIGN KEY (`ID_Cliente`) REFERENCES `clienti` (`ID_Cliente`),
  ADD CONSTRAINT `ordini_ibfk_2` FOREIGN KEY (`ID_Dipendente`) REFERENCES `dipendenti` (`ID_Dipendente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
