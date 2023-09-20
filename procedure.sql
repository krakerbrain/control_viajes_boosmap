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