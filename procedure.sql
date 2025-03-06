DELIMITER $$
CREATE PROCEDURE `ActualizarUsuario`(IN `p_idusuario` INT, IN `p_correo` VARCHAR(255), IN `p_password` VARCHAR(255), OUT `p_respuesta` VARCHAR(255), OUT `v_respuesta` JSON)
BEGIN
  -- Describe what this procedure does.

  DECLARE v_correo_existente INT;
  DECLARE v_correo_actual VARCHAR(255);

  -- Get the current email address of the user.
  SELECT correo INTO v_correo_actual FROM usuarios WHERE idusuario = p_idusuario;

  -- Initialize the JSON object with default values
  SET v_respuesta = JSON_OBJECT('correo', 'false', 'clave', 'false', 'correo_actual', v_correo_actual);

 -- Check if the email has been changed.
  IF p_correo <> v_correo_actual AND p_correo <> '' THEN
    -- Check if the email is already in use.
    SELECT COUNT(*) INTO v_correo_existente FROM usuarios WHERE correo = p_correo;
    IF v_correo_existente > 0 THEN
      SET p_respuesta = 'El correo ya está en uso.';
    ELSEIF NOT p_correo REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$' THEN
      SET p_respuesta = 'Formato de correo incorrecto';
    ELSE
      -- Update the email address.
      UPDATE usuarios SET correo = p_correo WHERE idusuario = p_idusuario;
      IF row_count() > 0 THEN
        SET p_respuesta = 'La actualización se ha realizado correctamente.';
        SET v_respuesta = JSON_SET(v_respuesta, '$.correo', 'true');
      END IF;
    END IF;
  END IF;

  IF p_password <> '' AND p_password IS NOT NULL THEN
    -- Update the password.
      UPDATE usuarios SET clave = p_password WHERE idusuario = p_idusuario;
      IF row_count() > 0 THEN
        SET p_respuesta = 'La actualización se ha realizado correctamente.';
        SET v_respuesta = JSON_SET(v_respuesta, '$.clave', 'true');
      END IF;
    ELSE
      SET p_respuesta = 'No han habido cambios';
    END IF;
END$$
DELIMITER ;

-- Se obtienen datos semanal, mensual y diario

DELIMITER //

CREATE PROCEDURE obtenerDatosPeriodicos(_idusuario INT)
BEGIN
    -- Crear tabla temporal para almacenar los resultados
    CREATE TEMPORARY TABLE IF NOT EXISTS resultados (
        periodo VARCHAR(10),
        viajes INT,
        total DECIMAL(10, 2)
    );

    -- Obtener datos de la semana
    INSERT INTO resultados (periodo, viajes, total)
    SELECT
        'semana' AS periodo,
        COUNT(*) AS viajes,
        SUM(monto) AS total
    FROM
        viajes
    WHERE
        idusuario = _idusuario
        AND YEARWEEK(fecha, 1) = YEARWEEK(CURRENT_DATE(), 1);

    -- Obtener datos del mes
    INSERT INTO resultados (periodo, viajes, total)
    SELECT
        'mes' AS periodo,
        COUNT(*) AS viajes,
        SUM(monto) AS total
    FROM
        viajes
    WHERE
        idusuario = _idusuario
        AND YEAR(fecha) = YEAR(CURRENT_DATE())
        AND MONTH(fecha) = MONTH(CURRENT_DATE());

    -- Obtener datos del día
    INSERT INTO resultados (periodo, viajes, total)
    SELECT
        'dia' AS periodo,
        COUNT(*) AS viajes,
        SUM(monto) AS total
    FROM
        viajes
    WHERE
        idusuario = _idusuario
        AND DATE(fecha) = DATE(CURRENT_DATE());

    -- Seleccionar los resultados de la tabla temporal
    SELECT * FROM resultados;

    -- Eliminar la tabla temporal
    DROP TEMPORARY TABLE IF EXISTS resultados;
END //

DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `detalles_viajes`(IN `_idusuario` INT, IN `_periodo` VARCHAR(10), IN `_fechaInicio` VARCHAR(10), IN `_fechaFin` VARCHAR(10), OUT `viajes` INT, OUT `total` INT)
CASE _periodo
WHEN 'mes' THEN
SELECT COUNT(*) , SUM(monto) into viajes, total FROM viajes WHERE idusuario = _idusuario and extract(month from fecha) = extract(month from now()) AND extract(year from fecha) = extract(year from now());
WHEN 'semana' THEN
SELECT COUNT(*) , SUM(monto) into viajes, total
FROM viajes
WHERE idusuario = _idusuario
  AND fecha >= _fechaInicio
  AND fecha <= _fechaFin;
WHEN 'hoy' THEN
SELECT COUNT(*) , SUM(monto) into viajes, total FROM viajes WHERE idusuario = _idusuario and DATE_FORMAT(fecha, '%Y-%m-%d') = DATE_FORMAT(date_add(now(),interval -3 hour), '%Y-%m-%d');
END CASE$$
DELIMITER ;



DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerDatosPeriodicos`(IN `_idusuario` INT, IN `_fechaHoy` VARCHAR(10))
BEGIN
    SELECT 
        1 AS idapp,
        'BOOSMAP' AS nombre_app,
        COALESCE(SUM(CASE WHEN periodo = 'dia' THEN total ELSE 0 END), 0) AS monto_dia,
        COALESCE(SUM(CASE WHEN periodo = 'semana' THEN total ELSE 0 END), 0) AS monto_semana,
        COALESCE(SUM(CASE WHEN periodo = 'mes' THEN total ELSE 0 END), 0) AS monto_mes
    FROM (
        SELECT 
            'semana' AS periodo,
            SUM(v.monto + COALESCE(extra_sum.monto_extra, 0)) as total
        FROM 
            viajes v
        LEFT JOIN 
            (SELECT idviaje, SUM(monto) AS monto_extra FROM extras GROUP BY idviaje) AS extra_sum 
            ON v.idviaje = extra_sum.idviaje
        WHERE 
            v.idusuario = _idusuario
            AND YEARWEEK(fecha, 1) = YEARWEEK(CURRENT_DATE(), 1)

        UNION ALL

        SELECT 
            'mes' as periodo,
            SUM(v.monto + COALESCE(extra_sum.monto_extra, 0)) as total
        FROM 
            viajes v
        LEFT JOIN 
            (SELECT idviaje, SUM(monto) AS monto_extra FROM extras GROUP BY idviaje) AS extra_sum 
            ON v.idviaje = extra_sum.idviaje
        WHERE 
            v.idusuario = _idusuario
            AND YEAR(fecha) = YEAR(CURRENT_DATE())
            AND MONTH(fecha) = MONTH(CURRENT_DATE())

        UNION ALL

        SELECT 
            'dia' as periodo,
            SUM(v.monto + COALESCE(extra_sum.monto_extra, 0)) as total
        FROM 
            viajes v
        LEFT JOIN 
            (SELECT idviaje, SUM(monto) AS monto_extra FROM extras GROUP BY idviaje) AS extra_sum 
            ON v.idviaje = extra_sum.idviaje
        WHERE 
            v.idusuario = _idusuario
                   AND DATE(fecha) = DATE(DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 3 HOUR))) AS periodo;
END$$
DELIMITER ;
