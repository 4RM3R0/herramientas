<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();
require '../../../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

if ((isset($_POST["registro"])) && ($_POST["registro"] == "formu")) {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $imagen = $_FILES['imagen'];

    // Generar un código de barras único
    $codigo_barras = uniqid() . rand(1000, 9999);

    $generator = new BarcodeGeneratorPNG();
    $codigo_barras_imagen = $generator->getBarcode($codigo_barras, $generator::TYPE_CODE_128);

    // Guardar el código de barras en un archivo
    file_put_contents(__DIR__ . '/../../../images/' . $codigo_barras . '.png', $codigo_barras_imagen);

    $fecha = date('Y-m-d');

    $extension = pathinfo($imagen["name"], PATHINFO_EXTENSION);
    $foto = $nombre . "-" . $fecha . "." . $extension;

    move_uploaded_file($imagen["tmp_name"], "../../../images/$foto");

    $validar = $conectar->prepare("SELECT codigo_barras FROM herrramienta WHERE codigo_barras = ?");
    $validar->execute([$codigo_barras]);
    $fila1 = $validar->fetch();

    if ($nombre == "" || $tipo == "" || $codigo_barras == "") {
        echo '<script> alert ("EXISTEN DATOS VACIOS");</script>';
        echo '<script> window.location="crear_herramientas.php"</script>';
    } else if ($fila1) {
        echo '<script> alert ("EL ARMA YA EXISTE");</script>';
        echo '<script> window.location= "lista.php"</script>';
    } else {
        $insertsql = $conectar->prepare("INSERT INTO herrramienta(nombre_he, id_cate,img_herramienta, estado, codigo_barras) VALUES (?, ?, ?, 'no prestada', ?)");
        $insertsql->execute([$nombre, $tipo, $foto, $codigo_barras]);
        echo '<script>alert ("Registro Exitoso");</script>';
        echo '<script> window.location= "lista.php"</script>';      
    }
}


// Consulta para obtener los tipos de armas
$tiposherrasQuery = $conectar->prepare("SELECT id_cate,categoria FROM categoria");
$tiposherrasQuery->execute();
$tiposherra = $tiposherrasQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <!-- site metas -->
    <title>limelight</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="../../../css/style.css">
    <!-- Responsive-->
    <link rel="stylesheet" href="../../../css/responsive.css">
    <!-- styles usuario -->
    <link rel="stylesheet" href="../../../css/styles_usuario.css">
    <!-- fevicon -->
    <link rel="icon" href="../../../images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="../../../css/jquery.mCustomScrollbar.min.css">
    <!-- Tweaks for older IEs-->
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>

<body class="main-layout in_page">
    <!-- header -->
    <header>
        <!-- header inner -->
        <div class="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-3 col logo_section">
                        <div class="full">
                            <div class="center-desk">
                                <div class="logo">
                                    <a href="index.html"><img src="../../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="contenedor sombra">
        <div class="container mt-5">
            <h2>Formulario de Registro</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre de la herramienta:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">



                    <div class="form-group">
                        <label for="tipo">Tipo de herramienta:</label>
                        <select class="form-control" id="tipo" name="tipo" required >
                            <option value="" disabled selected>Selecciona un tipo de arma</option> <!-- Placeholder -->
                            <?php foreach ($tiposherra as $tipoherra) : ?>
                                <option value="<?php echo $tipoherra['id_cate']; ?>"><?php echo $tipoherra['categoria']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="imagen">Imagen:</label>
                        <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*" required>
                    </div>

                    <input type="submit" class="btn btn-success" value="Registrate">
                    <input type="hidden" name="registro" value="formu">
                    <a href="lista.php" class="btn btn-danger">Volver</a>
            </form>
        </div>
    </main>
    <!-- footer -->
    <footer>
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class=" col-md-3 col-sm-6">
                        <ul class="social_icon">
                            <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                        </ul>
                        <p class="variat pad_roght2">There are many variat
                            ions of passages of L
                            orem Ipsum available
                            , but the majority h
                            ave suffered altera
                            tion in some form, by
                        </p>
                    </div>
                    <div class=" col-md-3 col-sm-6">
                        <h3>LET US HELP YOU </h3>
                        <p class="variat pad_roght2">There are many variat
                            ions of passages of L
                            orem Ipsum available
                            , but the majority h
                            ave suffered altera
                            tion in some form, by
                        </p>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <h3>INFORMATION</h3>
                        <ul class="link_menu">
                        </ul>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <h3>OUR Design</h3>
                        <p class="variat">There are many variat
                            ions of passages of L
                            orem Ipsum available
                            , but the majority h
                            ave suffered altera
                            tion in some form, by
                        </p>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <div class="container">
                    <div class="row">
                        <div class="col-md-10 offset-md-1">
                            <p>© 2019 All Rights Reserved. Design by <a href="https://html.design/"> Free Html Templates</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end footer -->
    <!-- Javascript files-->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- sidebar -->
    <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>