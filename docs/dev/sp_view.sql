-- 
-- Set character set the client will use to send SQL statements to the server
--
SET NAMES 'utf8';

--
-- Set default database
--
USE db_legion;

DELIMITER $$

--
-- Create procedure `sp_view`
--
CREATE PROCEDURE sp_view (IN `p_Tabla` longtext,
IN `p_where` char(1),
IN `p_col` varchar(50),
IN `p_valcol` varchar(50))
SQL SECURITY INVOKER
MODIFIES SQL DATA
BEGIN


  DECLARE i int UNSIGNED DEFAULT 0;
  DECLARE v_count int UNSIGNED DEFAULT 0;
  DECLARE v_current_item_tables longtext DEFAULT NULL;
  DECLARE v_current_item_campos longtext DEFAULT NULL;
  DECLARE v_current_item_relacion longtext DEFAULT NULL;
  DECLARE vartext longtext DEFAULT NULL;
  DECLARE param_tabla longtext DEFAULT NULL;
  SELECT
    DATABASE() INTO @DB;

  SET param_tabla = (SELECT
      AES_DECRYPT(UNHEX(p_Tabla), 'passwordSegura'));


  SET @array_table = (SELECT
      GROUP_CONCAT(CONCAT('"', referenced_table_name, '"'))
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE CONSTRAINT_SCHEMA = @DB
    AND TABLE_NAME = param_tabla
    AND REFERENCED_TABLE_SCHEMA = @DB);

  SET @foren_key = (SELECT
      GROUP_CONCAT(CONCAT('"', referenced_column_name, '"'))
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE CONSTRAINT_SCHEMA = @DB
    AND TABLE_NAME = param_tabla
    AND REFERENCED_TABLE_SCHEMA = @DB);

  SET @column_name = (SELECT
      GROUP_CONCAT(CONCAT('"', column_name, '"'))
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE CONSTRAINT_SCHEMA = @DB
    AND TABLE_NAME = param_tabla
    AND REFERENCED_TABLE_SCHEMA = @DB);

  SET @array_table = (SELECT
      CONCAT('[', @array_table, ']'));

  SET @relaciones_table = (SELECT
      CONCAT('[', @relaciones_table, ']'));
  SET @foren_key = (SELECT
      CONCAT('[', @foren_key, ']'));
  SET @column_name = (SELECT
      CONCAT('[', @column_name, ']'));

  SET v_count = (SELECT
      JSON_LENGTH(@array_table));

  SET vartext = "";

  WHILE i < v_count DO
    SET v_current_item_tables := JSON_EXTRACT(@ARRAY_TABLE, CONCAT('$[', i, ']'));
    SET v_current_item_campos := JSON_EXTRACT(@FOREN_KEY, CONCAT('$[', i, ']'));
    SET v_current_item_relacion := JSON_EXTRACT(@COLUMN_NAME, CONCAT('$[', i, ']'));
    SELECT
      REPLACE(v_current_item_tables, '\\', '') INTO v_current_item_tables;
    SELECT
      REPLACE(v_current_item_tables, '"(', '(') INTO v_current_item_tables;
    SELECT
      REPLACE(v_current_item_tables, ')"', ')') INTO v_current_item_tables;
    SELECT
      REPLACE(v_current_item_tables, '(', '[') INTO v_current_item_tables;
    SELECT
      REPLACE(v_current_item_tables, ')', ']') INTO v_current_item_tables;
    SELECT
      REPLACE(v_current_item_campos, '\\', '') INTO v_current_item_campos;
    SELECT
      REPLACE(v_current_item_campos, '"(', '(') INTO v_current_item_campos;
    SELECT
      REPLACE(v_current_item_campos, ')"', ')') INTO v_current_item_campos;
    SELECT
      REPLACE(v_current_item_campos, '(', '[') INTO v_current_item_campos;
    SELECT
      REPLACE(v_current_item_campos, ')', ']') INTO v_current_item_campos;
    SELECT
      REPLACE(v_current_item_relacion, '\\', '') INTO v_current_item_relacion;
    SELECT
      REPLACE(v_current_item_relacion, '"(', '(') INTO v_current_item_relacion;
    SELECT
      REPLACE(v_current_item_relacion, ')"', ')') INTO v_current_item_relacion;
    SELECT
      REPLACE(v_current_item_relacion, '(', '[') INTO v_current_item_relacion;
    SELECT
      REPLACE(v_current_item_relacion, ')', ']') INTO v_current_item_relacion;
    SET @p_tabla = JSON_EXTRACT(v_current_item_tables, '$[0]');
    SET @p_campo = JSON_EXTRACT(v_current_item_campos, '$[0]');
    SET @p_id = JSON_EXTRACT(v_current_item_relacion, '$[0]');
    SET @p_tabla = (SELECT
        REPLACE(@p_tabla, '"', ''));
    SET @p_campo = (SELECT
        REPLACE(@p_campo, '"', ''));
    SET @p_id = (SELECT
        REPLACE(@p_id, '"', ''));
    SET vartext = CONCAT(vartext, 'INNER JOIN ', @p_tabla, ' AS ', @p_tabla, i, ' on ', @p_tabla, i, ".", @p_campo, '=', param_tabla, ".", @p_id, '\r\n');
    SET i := i + 1;
  END WHILE;

  IF (p_where = '0') THEN
    SET @vista = CONCAT('SELECT * FROM ', param_tabla, ' ', vartext);
    PREPARE stmt FROM @vista;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  ELSE
    IF (p_where = '1'
      AND p_col <> ''
      AND p_col <> '0') THEN

      SET @vista = CONCAT('SELECT * FROM ', param_tabla, '  ', vartext, ' ', 'where', '  ', param_tabla, '.', p_col, ' ', 'IN', ' ', '(', p_valcol, ')');
      PREPARE stmt FROM @vista;
      EXECUTE stmt;
      DEALLOCATE PREPARE stmt;

    ELSE
      IF (p_where = '1'
        AND p_col = '0')
        OR (p_where = '1'
        AND p_col = '') THEN
        SELECT
          'FALSE';
      END IF;
    END IF;
  END IF;
END
$$

DELIMITER ;