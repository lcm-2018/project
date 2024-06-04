CREATE TABLE `acf_orden_ingreso_detalle` (
  `id_ing_detalle` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_orden_ingreso` int(10) unsigned DEFAULT NULL,
  `id_medicamento_articulo` int(10) DEFAULT NULL,
  `observacion` varchar(300) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `valor_sin_iva` decimal(16,4) DEFAULT NULL,
  `iva` decimal(5,2) DEFAULT NULL,
  `valor` decimal(16,4) DEFAULT NULL,
  PRIMARY KEY (`id_ing_detalle`),
  KEY `FK_acf_orden_ingreso_detalle1` (`id_orden_ingreso`),
  KEY `FK_acf_orden_ingreso_articulo2` (`id_medicamento_articulo`),
  CONSTRAINT `FK_acf_orden_ingreso_articulo2` FOREIGN KEY (`id_medicamento_articulo`) REFERENCES `far_medicamentos` (`id_med`),
  CONSTRAINT `FK_acf_orden_ingreso_detalle1` FOREIGN KEY (`id_orden_ingreso`) REFERENCES `acf_orden_ingreso` (`id_ingreso`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
