CREATE TABLE `acf_detalle_mantenimiento_nota` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_detalle_mantenimiento` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora` char(7) NOT NULL,
  `observaciones` varchar(500) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `id_usuario_crea` int(10) unsigned NOT NULL,
  `archivo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_USUARIO_CREA_idx` (`id_usuario_crea`),
  KEY `FK_MANTENIMIENTO_DETALLE_idx` (`id_detalle_mantenimiento`),
  CONSTRAINT `FK_MANTENIMIENTO_DETALLE` FOREIGN KEY (`id_detalle_mantenimiento`) REFERENCES `acf_mantenimiento_detalle` (`id_detalle_mantenimiento`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_USUARIO_CREA` FOREIGN KEY (`id_usuario_crea`) REFERENCES `seg_usuarios_sistema` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
