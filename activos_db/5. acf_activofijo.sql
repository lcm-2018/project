CREATE TABLE `acf_activofijo` (
  `placa` varchar(40) NOT NULL,
  `serial` varchar(40) NOT NULL,
  `id_marca` int(11) DEFAULT NULL,
  `valor` decimal(16,4) DEFAULT NULL,
  `tipo_activo` int(11) DEFAULT NULL,
  `id_articulo` int(10) DEFAULT NULL,
  PRIMARY KEY (`placa`),
  KEY `FK_ID_ARCA_idx` (`id_marca`),
  KEY `FK_ID_ARTICULO_idx` (`id_articulo`),
  CONSTRAINT `FK_ID_ARTICULO` FOREIGN KEY (`id_articulo`) REFERENCES `far_medicamentos` (`id_med`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_ID_MARCA` FOREIGN KEY (`id_marca`) REFERENCES `acf_marca` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
