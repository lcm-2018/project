CREATE TABLE `acf_activofijo_ordeningresodetalle` (
  `id_ordeningresodetalle` int(10) unsigned NOT NULL,
  `placa_activofijo` varchar(40) NOT NULL,
  UNIQUE KEY `placa_activofijo_UNIQUE` (`placa_activofijo`),
  KEY `FK_id_ordeningresodetalle_idx` (`id_ordeningresodetalle`),
  KEY `FK_placa_activofijo_idx` (`placa_activofijo`),
  CONSTRAINT `FK_id_ordeningresodetalle` FOREIGN KEY (`id_ordeningresodetalle`) REFERENCES `acf_orden_ingreso_detalle` (`id_ing_detalle`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_placa_activofijo` FOREIGN KEY (`placa_activofijo`) REFERENCES `acf_activofijo` (`placa`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
