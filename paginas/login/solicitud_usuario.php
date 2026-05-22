<?php
    session_start();

    // datos de instituciones
    require_once "../../php/config.php";
    $db = new database();
    $conex = $db->conectar();

    $datos_institucion = $conex->prepare("SELECT * FROM institucion");
    $datos_institucion->execute();
    $resultado_instituciones = $datos_institucion->fetchAll(PDO::FETCH_ASSOC);

    // transportes
    $consulta_transporte = $conex->prepare("SELECT * FROM transporte");
    $consulta_transporte->execute([]);
    $resultado_t = $consulta_transporte->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de control usuario</title>

    <link rel="icon" href="../../imagenes/logo.ico" type="image/x-icon">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../../css/notificacion.css">
    <link rel="stylesheet" href="../../css/normalize.css">
    <link rel="stylesheet" href="../../css/registro_usuario/stile.css">
    <link rel="stylesheet" href="../../css/solicitud/solicitud.css">

    <!-- CSS boostrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Font awesome -->
    <script src="https://kit.fontawesome.com/7881bda97f.js" crossorigin="anonymous"></script>

    <!-- Mapas API -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>
<body>
    <!-- --- Notificacion --- -->
    <!-- funcionalidad de envio de una respuesta a traves de php -->
    <?php if (isset($_SESSION['icono_n'])){ ?>
    <div class="capa_notificacion ani_entrada_notificacion">
        <div class="contenedor_notificacion" id="contenedor_notificacion">
            <div class="encabezado">
                <i class="<?php echo $_SESSION['icono_n'] ?>"></i>
                <p class="titulo"><?php echo $_SESSION['titulo_n'] ?></p>
            </div>
            
            <p class="contenido"><?php echo $_SESSION['respuesta_n'] ?></p>
            <button class="btn1" id="btn_cerrar_notificacion">Okey</button>
        </div>
    </div>
    <?php
        unset($_SESSION['icono_n'], $_SESSION['titulo_n'], $_SESSION['respuesta_n']); 
        } else { ?>
        <div class="capa_notificacion">
            <div class="contenedor_notificacion" id="contenedor_notificacion">
                <div class="encabezado">
                    <i class=""></i>
                    <p class="titulo">Titulo</p>
                </div>
                
                <p class="contenido">Contenido</p>
                <button class="btn1" id="btn_cerrar_notificacion">Aceptar</button>
            </div>
        </div>
    <?php } ?>

    <main>
        <form class="item_registro contenido_registro" method="POST" action="../../php/solicitud/enviar_solicitud_usuario.php" id="frm_solicitud" enctype="multipart/form-data">
            <div class="contenedor_wave"></div>

            <h1><i class="fa-solid fa-paste"></i> solicitud del usuario</h1>

            <div class="contenedor_casillas">
                <div class="item_casilla">
                    *<i class="fa-solid fa-signature"></i>
                    <input type="text" name="nombre" placeholder="Nombre" required>
                </div>

                <div class="item_casilla">
                    *<i class="fa-solid fa-key"></i>
                    <input type="text" name="contraseña" placeholder="Contraseña" required id="contraseña1">
                </div>

                <div class="item_casilla">
                    *<i class="fa-solid fa-key"></i>
                    <input type="text" name="c_contraseña" placeholder="Confirmar contraseña" required id="contraseña2">
                </div>

                <div class="item_casilla">
                    *<i class="fa-solid fa-envelope"></i>
                    <input type="text" name="correo" placeholder="Correo Electrónico" required>
                </div>

                <div class="item_casilla">
                    *<i class="fa-solid fa-phone"></i>
                    <input type="text" name="telefono" placeholder="Teléfono: xxx-xxx-xxxx" required>
                </div>

                <div class="item_casilla">
                    *<i class="fa-solid fa-map-location-dot"></i>
                    <input type="text" name="direccion" placeholder="Dirección" id="direccion" required>
                </div>

                <div class="item_casilla">
                    *<i class="fa-solid fa-building"></i>

                    <input type="text" name="vinculo_institucion" list="lista_instituciones" placeholder="Institución o empresa">
                    <datalist id="lista_instituciones">
                        <!-- Filtrando instituciones -->
                        <?php foreach($resultado_instituciones as $row) { ?>
                            <option value="<?php echo $row['nombre_institucion'] ?>">Nombre de la institución</option>
                        <?php } ?>
                    </datalist>
                </div>

                <div class="item_casilla transporte">
                    <div class="subcontenedor_casilla">
                        <i class="fa-solid fa-car-rear"></i>
                        <select require id="transporte_select">
                            <option value="Seleccionar transporte" disabled selected>Seleccionar transporte</option>
                            <?php foreach($resultado_t as $row){ ?>
                                <option value="<?php echo $row['nombre'] ?>"><?php echo $row['nombre'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="elecciones" id="visualizador_elecciones">
                        <!-- <button class="item_eleccion">
                            <p>Omsa</p>
                            <input type="number" id="cantidad_transporte" placeholder="1" min="0">
                            <i class="fa-solid fa-trash ani1_icon"></i> 
                        </button> -->

                        <input type="text" name="transporte" id="transporte_input">
                    </div>
                </div>

                <div class="item_casilla imagen">
                    <span>
                       <img src="../../imagenes/avatar.png" alt="" id="imagen">
                    </span>

                    <label for="btn_img" class="btn_img btn1">
                    <i class="fa-solid fa-images"></i> Subir imagen</label>
                    <input type="file" name="imagen_usuario" id="btn_img">
                </div>
            </div>

            <div class="controles">
                <button type="submit" id="btn_register_solicitud" class="btn_register_solicitud btn1" name="btn_register_solicitud">
                    <i class="fa-solid fa-share"></i>
                    Enviar solicitud
                </button>

                <a href="../../index.php" class="btn1">
                <i class="fa-solid fa-house"></i>    
                Volver</a>
            </div>
        </form>

        <div class="item_registro contenedor_img">
            <img src="../../imagenes/IMG_7403.webp" alt="">
        </div>
    </main>

    <!-- Js personalizado -->
    <script src="../../js/notificacion.js"></script>
    <script src="../../js/solicitud/solicitud.js"></script>
    <script src="../../js/solicitud/seleccion_transporte.js"></script>
    <script src="../../js/solicitud/verificar_direccion.js"></script>
</body>
</html>
