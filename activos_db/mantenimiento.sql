CREATE TABLE `acf_mantenimiento` (
  `id_mantenimiento` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fecha_mantenimiento` date DEFAULT NULL,
  `hora_mantenimiento` char(7) NOT NULL,
  `observaciones` varchar(300) NOT NULL,
  `tipo_mantenimiento` int(10) NOT NULL,
  `id_responsable` int(10) unsigned NOT NULL,
  `id_tercero` int(11) NOT NULL,
  `fecha_inicio_mantenimiento` date NOT NULL,
  `fecha_fin_mantenimiento` date DEFAULT NULL,
  `estado` int(10) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `usuaro_creacion` int(10) unsigned NOT NULL,
  `fecha_aprobacion` date DEFAULT NULL,
  `usuario_aprobacion` int(10) unsigned DEFAULT NULL,
  `fecha_ejecucion` date DEFAULT NULL,
  `usuario_ejecucion` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_mantenimiento`),
  KEY `FK_RESPONSABLE_idx` (`id_responsable`),
  KEY `FK_TERCERO_idx` (`id_tercero`),
  KEY `FK_USUARIO_CREACION_idx` (`usuaro_creacion`),
  KEY `FK_USUARIO_EJECUCION_idx` (`usuario_ejecucion`),
  KEY `FK_USUARIO_APROBACION_idx` (`usuario_aprobacion`),
  CONSTRAINT `FK_RESPONSABLE` FOREIGN KEY (`id_responsable`) REFERENCES `seg_usuarios_sistema` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_TERCERO` FOREIGN KEY (`id_tercero`) REFERENCES `tb_terceros` (`id_tercero`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_USUARIO_APROBACION` FOREIGN KEY (`usuario_aprobacion`) REFERENCES `seg_usuarios_sistema` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_USUARIO_CREACION` FOREIGN KEY (`usuaro_creacion`) REFERENCES `seg_usuarios_sistema` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_USUARIO_EJECUCION` FOREIGN KEY (`usuario_ejecucion`) REFERENCES `seg_usuarios_sistema` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;


CREATE TABLE `acf_mantenimiento_detalle` (
  `id_detalle_mantenimiento` int(10) NOT NULL,
  `id_mantenimiento` int(10) unsigned DEFAULT NULL,
  `id_activo_fijo` int(10) unsigned DEFAULT NULL,
  `observacion_mantenimiento` varchar(45) DEFAULT NULL,
  `estado_fin_mantenimiento` varchar(45) DEFAULT NULL,
  `observacio_fin_mantenimiento` varchar(45) DEFAULT NULL,
  `estado` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_detalle_mantenimiento`),
  KEY `FK_ACTIVOFIJO_idx` (`id_activo_fijo`),
  KEY `FK_MANTENIMIENTO_idx` (`id_mantenimiento`),
  CONSTRAINT `FK_ACTIVOFIJO` FOREIGN KEY (`id_activo_fijo`) REFERENCES `acf_hojavida` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_MANTENIMIENTO` FOREIGN KEY (`id_mantenimiento`) REFERENCES `acf_mantenimiento` (`id_mantenimiento`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

