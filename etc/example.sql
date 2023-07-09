SELECT 
  tbl_persona.per_intId, 
  tbl_persona.per_varIdentificacion, 
  tbl_persona.per_varDV, 
  tbl_persona.per_varRazonSocial, 
  tbl_persona.per_varNombre, 
  tbl_persona.per_varNombre2, 
  tbl_persona.per_varApellido, 
  tbl_persona.per_varApellido2, 
  tbl_persona.per_varNombreCompleto, 
  tbl_persona.per_varDireccion, 
  tbl_persona.per_varCelular, 
  tbl_persona.per_varTelefono, 
  tbl_persona.per_varEmail, 
  tbl_persona.per_datFechaNacimiento, 
  tbl_persona.per_dtimFechaCreacion, 
  tbl_persona.per_dtimFechaActualizacion, 
  tbl_persona.tpr_intIdTipoPersona, 
  tbl_persona.pai_intIdPais, 
  tbl_persona.ciu_intIdCiudad, 
  tbl_persona.gen_intIdGenero, 
  tbl_persona.cid_intIdCategoriIdentificacion, 
  tbl_persona.est_intIdEstado, 
  tbl_persona.usu_intIdCreador, 
  tbl_persona.usu_intIdActualizador, 
  (
    SELECT 
      IF(
        COUNT(__tbl_estado.est_intId) = 0, 
        '', 
        JSON_OBJECT(
          'est_intId', __tbl_estado.est_intId, 
          'est_varNombre', __tbl_estado.est_varNombre, 
          'est_varIcono', __tbl_estado.est_varIcono, 
          'est_varColor', __tbl_estado.est_varColor, 
          'est_dtimFechaCreacion', __tbl_estado.est_dtimFechaCreacion, 
          'est_dtimFechaActualizacion', __tbl_estado.est_dtimFechaActualizacion, 
          'usu_intIdCreador', __tbl_estado.usu_intIdCreador, 
          'usu_intIdActualizador', __tbl_estado.usu_intIdActualizador
        )
      ) 
    FROM 
      tbl_estado as __tbl_estado 
    WHERE 
      tbl_persona.est_intIdEstado = __tbl_estado.est_intId
  ) as tbl_estado, 
  (
    SELECT 
      IF(
        COUNT(
          __usu_intIdActualizador.usu_intId
        ) = 0, 
        JSON_ARRAY(), 
        JSON_ARRAYAGG(
          JSON_OBJECT(
            'usu_intId', __usu_intIdActualizador.usu_intId, 
            'usu_varNroIdentificacion', __usu_intIdActualizador.usu_varNroIdentificacion, 
            'usu_varNombre', __usu_intIdActualizador.usu_varNombre, 
            'usu_varNombre2', __usu_intIdActualizador.usu_varNombre2, 
            'usu_varApellido', __usu_intIdActualizador.usu_varApellido, 
            'usu_varApellido2', __usu_intIdActualizador.usu_varApellido2, 
            'usu_varNombreCompleto', __usu_intIdActualizador.usu_varNombreCompleto, 
            'usu_varEmail', __usu_intIdActualizador.usu_varEmail, 
            'usu_varNumeroCelular', __usu_intIdActualizador.usu_varNumeroCelular, 
            'usu_varExtension', __usu_intIdActualizador.usu_varExtension, 
            'usu_varPassword', __usu_intIdActualizador.usu_varPassword, 
            'usu_varToken', __usu_intIdActualizador.usu_varToken, 
            'usu_varTokenContrasena', __usu_intIdActualizador.usu_varTokenContrasena, 
            'usu_bolGetContrasena', __usu_intIdActualizador.usu_bolGetContrasena, 
            'usu_bolEstadoUsuario', __usu_intIdActualizador.usu_bolEstadoUsuario, 
            'usu_varImagen', __usu_intIdActualizador.usu_varImagen, 
            'usu_intNumeroIntentos', __usu_intIdActualizador.usu_intNumeroIntentos, 
            'usu_dtimFechaCreacion', __usu_intIdActualizador.usu_dtimFechaCreacion, 
            'usu_dtimFechaActualizacion', __usu_intIdActualizador.usu_dtimFechaActualizacion, 
            'usu_dtimFechaRecuperacion', __usu_intIdActualizador.usu_dtimFechaRecuperacion, 
            'est_intIdEstado', __usu_intIdActualizador.est_intIdEstado, 
            'rol_intIdRol', __usu_intIdActualizador.rol_intIdRol, 
            'car_intIdCargo', __usu_intIdActualizador.car_intIdCargo, 
            'cdo_intIdCategoriaDocumento', 
            __usu_intIdActualizador.cdo_intIdCategoriaDocumento
          )
        )
      ) 
    FROM 
      tbl_usuario as __usu_intIdActualizador 
    WHERE 
      tbl_persona.usu_intIdActualizador = __usu_intIdActualizador.usu_intId
  ) as __usu_intIdActualizador, 
  (
    SELECT 
      IF(
        COUNT(__usu_intIdCreador.usu_intId) = 0, 
        JSON_ARRAY(), 
        JSON_ARRAYAGG(
          JSON_OBJECT(
            'usu_intId', __usu_intIdCreador.usu_intId, 
            'usu_varNroIdentificacion', __usu_intIdCreador.usu_varNroIdentificacion, 
            'usu_varNombre', __usu_intIdCreador.usu_varNombre, 
            'usu_varNombre2', __usu_intIdCreador.usu_varNombre2, 
            'usu_varApellido', __usu_intIdCreador.usu_varApellido, 
            'usu_varApellido2', __usu_intIdCreador.usu_varApellido2, 
            'usu_varNombreCompleto', __usu_intIdCreador.usu_varNombreCompleto, 
            'usu_varEmail', __usu_intIdCreador.usu_varEmail, 
            'usu_varNumeroCelular', __usu_intIdCreador.usu_varNumeroCelular, 
            'usu_varExtension', __usu_intIdCreador.usu_varExtension, 
            'usu_varPassword', __usu_intIdCreador.usu_varPassword, 
            'usu_varToken', __usu_intIdCreador.usu_varToken, 
            'usu_varTokenContrasena', __usu_intIdCreador.usu_varTokenContrasena, 
            'usu_bolGetContrasena', __usu_intIdCreador.usu_bolGetContrasena, 
            'usu_bolEstadoUsuario', __usu_intIdCreador.usu_bolEstadoUsuario, 
            'usu_varImagen', __usu_intIdCreador.usu_varImagen, 
            'usu_intNumeroIntentos', __usu_intIdCreador.usu_intNumeroIntentos, 
            'usu_dtimFechaCreacion', __usu_intIdCreador.usu_dtimFechaCreacion, 
            'usu_dtimFechaActualizacion', __usu_intIdCreador.usu_dtimFechaActualizacion, 
            'usu_dtimFechaRecuperacion', __usu_intIdCreador.usu_dtimFechaRecuperacion, 
            'est_intIdEstado', __usu_intIdCreador.est_intIdEstado, 
            'rol_intIdRol', __usu_intIdCreador.rol_intIdRol, 
            'car_intIdCargo', __usu_intIdCreador.car_intIdCargo, 
            'cdo_intIdCategoriaDocumento', 
            __usu_intIdCreador.cdo_intIdCategoriaDocumento
          )
        )
      ) 
    FROM 
      tbl_usuario as __usu_intIdCreador 
    WHERE 
      tbl_persona.usu_intIdActualizador = __usu_intIdCreador.usu_intId
  ) as __usu_intIdCreador, 
  (
    SELECT 
      IF(
        COUNT(__tbl_tipo_persona.tpr_intId) = 0, 
        '', 
        JSON_OBJECT(
          'tpr_intId', __tbl_tipo_persona.tpr_intId, 
          'tpr_varNombre', __tbl_tipo_persona.tpr_varNombre, 
          'tpr_dtimFechaCreacion', __tbl_tipo_persona.tpr_dtimFechaCreacion, 
          'tpr_dtimFechaActualizacion', __tbl_tipo_persona.tpr_dtimFechaActualizacion, 
          'usu_intIdCreador', __tbl_tipo_persona.usu_intIdCreador, 
          'usu_intIdActualizador', __tbl_tipo_persona.usu_intIdActualizador
        )
      ) 
    FROM 
      tbl_tipo_persona as __tbl_tipo_persona 
    WHERE 
      tbl_persona.tpr_intIdTipoPersona = __tbl_tipo_persona.tpr_intId
  ) as tbl_tipo_persona, 
  (
    SELECT 
      IF(
        COUNT(__tbl_pais.pai_intId) = 0, 
        '', 
        JSON_OBJECT(
          'pai_intId', __tbl_pais.pai_intId, 
          'pai_varCodigo', __tbl_pais.pai_varCodigo, 
          'pai_varPais', __tbl_pais.pai_varPais, 
          'pai_varCodigoPaisCelular', __tbl_pais.pai_varCodigoPaisCelular, 
          'pai_dtimFechaCreacion', __tbl_pais.pai_dtimFechaCreacion, 
          'pai_dtimFechaActualizacion', __tbl_pais.pai_dtimFechaActualizacion, 
          'est_intIdEstado', __tbl_pais.est_intIdEstado, 
          'pai_intIdMoneda', __tbl_pais.pai_intIdMoneda, 
          'usu_intIdCreador', __tbl_pais.usu_intIdCreador, 
          'usu_intIdActualizador', __tbl_pais.usu_intIdActualizador
        )
      ) 
    FROM 
      tbl_pais as __tbl_pais 
    WHERE 
      tbl_persona.pai_intIdPais = __tbl_pais.pai_intId
  ) as tbl_pais, 
  (
    SELECT 
      IF(
        COUNT(__tbl_ciudad.ciu_intId) = 0, 
        '', 
        JSON_OBJECT(
          'ciu_intId', __tbl_ciudad.ciu_intId, 
          'ciu_varCodigo', __tbl_ciudad.ciu_varCodigo, 
          'ciu_varCiudad', __tbl_ciudad.ciu_varCiudad, 
          'ciu_varIndicativoTelefono', __tbl_ciudad.ciu_varIndicativoTelefono, 
          'ciu_dtimFechaCreacion', __tbl_ciudad.ciu_dtimFechaCreacion, 
          'ciu_dtimFechaActualizacion', __tbl_ciudad.ciu_dtimFechaActualizacion, 
          'est_intIdEstado', __tbl_ciudad.est_intIdEstado, 
          'pai_intIdPais', __tbl_ciudad.pai_intIdPais, 
          'dep_intIdDepartamento', __tbl_ciudad.dep_intIdDepartamento, 
          'usu_intIdCreador', __tbl_ciudad.usu_intIdCreador, 
          'usu_intIdActualizador', __tbl_ciudad.usu_intIdActualizador
        )
      ) 
    FROM 
      tbl_ciudad as __tbl_ciudad 
    WHERE 
      tbl_persona.ciu_intIdCiudad = __tbl_ciudad.ciu_intId
  ) as tbl_ciudad, 
  (
    SELECT 
      IF(
        COUNT(__tbl_genero.gen_intId) = 0, 
        '', 
        JSON_OBJECT(
          'gen_intId', __tbl_genero.gen_intId, 
          'gen_varGenero', __tbl_genero.gen_varGenero, 
          'gen_dtimFechaCreacion', __tbl_genero.gen_dtimFechaCreacion, 
          'est_intIdEstado', __tbl_genero.est_intIdEstado, 
          'usu_intIdCreador', __tbl_genero.usu_intIdCreador, 
          'usu_intIdActualizador', __tbl_genero.usu_intIdActualizador, 
          'gen_dtimFechaActualizacion', __tbl_genero.gen_dtimFechaActualizacion
        )
      ) 
    FROM 
      tbl_genero as __tbl_genero 
    WHERE 
      tbl_persona.gen_intIdGenero = __tbl_genero.gen_intId
  ) as tbl_genero, 


  
   (SELECT CONCAT( ' [' ,GROUP_CONCAT( JSON_OBJECT(
       'cap_intId',tcp.cap_intId
       ,   'cap_varCategoriaPersona',tcp.cap_varCategoriaPersona
       ,   'cap_dtimFechaCreacion',tcp.cap_dtimFechaCreacion
       ,   'cap_dtimFechaActualizacion',tcp.cap_dtimFechaActualizacion
       ,   'est_intIdEstado',tcp.est_intIdEstado
       ,   'usu_intIdCreador',tcp.usu_intIdCreador
          ,   'usu_intIdActualizador',tcp.usu_intIdActualizador
	    
	    )),']')
       FROM 
        tbl_categoria_persona as tcp
       INNER JOIN tbl_categoria_persona_persona as tcpp ON tcp.cap_intId = tcpp.cap_intIdCategoriaPersona
       WHERE tcpp.per_intIdPersona = tbl_persona.per_intId
       ) as tbl_categoria_persona



FROM 
  tbl_persona 
LIMIT 
  10
