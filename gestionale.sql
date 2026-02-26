-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 26, 2026 alle 08:36
-- Versione del server: 10.4.28-MariaDB
-- Versione PHP: 8.0.28

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
(1, 1, 'croix89', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8', 'Marco', 'Merrino', 'Marcomerrino89@gmail.com', '2026-02-26', 2345.00, 'IBAN', 'Boss', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `filiale_magazzini`
--

CREATE TABLE `filiale_magazzini` (
  `id_filiale` int(11) NOT NULL,
  `ID_Magazzino` int(11) NOT NULL
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
-- Struttura della tabella `ordine_prodotti`
--

CREATE TABLE `ordine_prodotti` (
  `ID_Ordine` int(11) NOT NULL,
  `ID_Prodotto` int(11) NOT NULL,
  `Quantita` int(11) DEFAULT NULL
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
  `Data_Arrivo` date DEFAULT NULL
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
-- Indici per le tabelle `filiale_magazzini`
--
ALTER TABLE `filiale_magazzini`
  ADD PRIMARY KEY (`id_filiale`,`ID_Magazzino`),
  ADD KEY `ID_Magazzino` (`ID_Magazzino`);

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
-- Indici per le tabelle `ordine_prodotti`
--
ALTER TABLE `ordine_prodotti`
  ADD PRIMARY KEY (`ID_Ordine`,`ID_Prodotto`),
  ADD KEY `ID_Prodotto` (`ID_Prodotto`);

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
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `clienti`
--
ALTER TABLE `clienti`
  MODIFY `ID_Cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `dipendenti`
--
ALTER TABLE `dipendenti`
  MODIFY `ID_Dipendente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `filiali`
--
ALTER TABLE `filiali`
  MODIFY `id_filiale` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `magazzini`
--
ALTER TABLE `magazzini`
  MODIFY `ID_Magazzino` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `ordini`
--
ALTER TABLE `ordini`
  MODIFY `ID_Ordine` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `prodotti`
--
ALTER TABLE `prodotti`
  MODIFY `ID_Prodotto` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `dipendenti`
--
ALTER TABLE `dipendenti`
  ADD CONSTRAINT `dipendenti_ibfk_1` FOREIGN KEY (`ID_Filiale`) REFERENCES `filiali` (`id_filiale`);

--
-- Limiti per la tabella `filiale_magazzini`
--
ALTER TABLE `filiale_magazzini`
  ADD CONSTRAINT `filiale_magazzini_ibfk_1` FOREIGN KEY (`id_filiale`) REFERENCES `filiali` (`id_filiale`),
  ADD CONSTRAINT `filiale_magazzini_ibfk_2` FOREIGN KEY (`ID_Magazzino`) REFERENCES `magazzini` (`ID_Magazzino`);

--
-- Limiti per la tabella `magazzino_prodotti`
--
ALTER TABLE `magazzino_prodotti`
  ADD CONSTRAINT `magazzino_prodotti_ibfk_1` FOREIGN KEY (`ID_Magazzino`) REFERENCES `magazzini` (`ID_Magazzino`),
  ADD CONSTRAINT `magazzino_prodotti_ibfk_2` FOREIGN KEY (`ID_Prodotto`) REFERENCES `prodotti` (`ID_Prodotto`);

--
-- Limiti per la tabella `ordine_prodotti`
--
ALTER TABLE `ordine_prodotti`
  ADD CONSTRAINT `ordine_prodotti_ibfk_1` FOREIGN KEY (`ID_Ordine`) REFERENCES `ordini` (`ID_Ordine`),
  ADD CONSTRAINT `ordine_prodotti_ibfk_2` FOREIGN KEY (`ID_Prodotto`) REFERENCES `prodotti` (`ID_Prodotto`);

--
-- Limiti per la tabella `ordini`
--
ALTER TABLE `ordini`
  ADD CONSTRAINT `ordini_ibfk_1` FOREIGN KEY (`ID_Cliente`) REFERENCES `clienti` (`ID_Cliente`),
  ADD CONSTRAINT `ordini_ibfk_2` FOREIGN KEY (`ID_Dipendente`) REFERENCES `dipendenti` (`ID_Dipendente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
