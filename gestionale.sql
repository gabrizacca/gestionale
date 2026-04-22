CREATE TABLE `fatture` (
  `ID_Fattura` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Ordine` int(11) NOT NULL,
  `Data_Emissione` date NOT NULL,
  `Importo_Totale` decimal(10, 2) NOT NULL,
  `Stato_Pagamento` enum('In attesa','Pagato','Annullato') DEFAULT 'In attesa',
  PRIMARY KEY (`ID_Fattura`),
  UNIQUE KEY `unique_ordine` (`ID_Ordine`),
  CONSTRAINT `fk_fattura_ordine` FOREIGN KEY (`ID_Ordine`) REFERENCES `ordini` (`ID_Ordine`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;