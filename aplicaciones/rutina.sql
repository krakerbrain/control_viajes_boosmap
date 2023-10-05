DELIMITER $$
CREATE PROCEDURE `RegistraGanancia`(IN `idAppIn` INT, IN `montoIn` DECIMAL(10,2), IN `idusuario` INT, IN `fechaHoy` DATE, IN `fechayHoraHoy` DATETIME)
BEGIN
    DECLARE id_viajes INT;
    DECLARE montoActual DECIMAL(10, 2);

    SELECT id_viajes_app INTO id_viajes FROM viajes_aplicaciones WHERE idusuario = idusuario AND idapp = idAppIn AND DATE(fecha) = fechaHoy LIMIT 1;
    IF id_viajes IS NOT NULL THEN
        SELECT monto INTO montoActual FROM viajes_aplicaciones WHERE id_viajes_app = id_viajes and DATE(fecha) = fechaHoy;

        IF montoIn > montoActual THEN
            UPDATE viajes_aplicaciones SET monto = montoIn, fecha = fechayHoraHoy WHERE idusuario = idusuario AND id_viajes_app = id_viajes;
            SELECT 'ok' AS resultado;
        ELSE
            SELECT 'El monto a agregar no es mayor al monto actual' AS resultado;
        END IF;
    ELSE
        INSERT INTO viajes_aplicaciones (idusuario, idapp, monto, fecha) VALUES (idusuario, idAppIn, montoIn, fechayHoraHoy);
        SELECT 'ok' AS resultado;
    END IF;
END$$
DELIMITER ;