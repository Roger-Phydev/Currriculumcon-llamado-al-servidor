<?php 
//conexión a servidor
require_once 'clases/server_conection.php'; //importamos la clase requerida
$conexion = new ServerConection("betaadryo.com.mx","betaadry_becarios","vekx6YRz2MgZ8HS");//creamos una conexión al servidor de adryo, con host, usuario y contraseña
$id_name_info="id";
$id_foreing = "informacion_personal_id";
$id_value="11";//definimos el valor de id y el id para obtener un solo registro
$db = "betaadry_ejercicio_01";
try{//intentamos la conexión para obtener datos para construir un CV
    //NOMBRE COMPLETO:
    $nombre_completo = $conexion->get_one_value($db,"informacion_personal","nombre_completo",$id_name_info,$id_value); //usando este método obtenemos el nombre completo como string
    //FOTO DE PERFIL:
    $foto_perfil = $conexion->get_one_value($db,"informacion_personal","foto_perfil",$id_name_info,$id_value);//con este método obtenemos la ruta de la foto como string
    //OBJETIVO PROFESIONAL:
    $objetivo_profesional = ($conexion->get_one_value($db,"objetivo_profesional","descripcion",$id_name_info,$id_value)?
    $conexion->get_one_value($db,"objetivo_profesional","descripcion",$id_name,$id_value) :
    "Desarrollador Web junior"); //en caso de ser vacío, ponemos un genérico solo para que esto no frene el avance, pues en el caso del servidor, no incluye información, pero en caso de fallar, por como construí la clase server_conection, devolverá un array vacío, lo cual, por construcción de la clase CV devolverá la información del error mostrando lo demás.
    //SOBRE MI:
    $sobre_mi="";//ponemos vacío, pues no existe esta información en la base de datos
    //CONTACTO :
    $contacto = $conexion->get_and_transform_to_array($db,"informacion_personal",["fecha_nacimiento","direccion","telefono","email"],$id_name_info,$id_value);//creamos un array usando este método con las key y usando id y su valor para solo obtener 1
    //EDUCACION :
    $educacion = $conexion->get_array_of_arrays($db,"educacion",["institucion","titulo","fecha_inicio","fecha_fin","descripcion"],$id_foreing,$id_value); //creamos un array de arrays a partir de la id foranea
    //HABILIDADES :
    $habilidades = $conexion->get_asociative_array_of_arrays($db,"habilidades","categoria","habilidad",$id_foreing,$id_value);//creamos un array asociativo usando el id foraneo.
    //EXPERIENCIA LABORAL :
    $experiencia_laboral = $conexion->get_array_of_arrays($db,"experiencia_laboral",["puesto","empresa","fecha_inicio","fecha_fin","responsabilidades"],$id_foreing,$id_value);
    //CERTIFICACIONES :
    $certificaciones = $conexion->get_array_of_arrays($db,"certificaciones",["nombre","institucion","fecha_obtencion"],$id_foreing,$id_value);
    //IDIOMAS :
    $idiomas = $conexion->get_array_of_arrays($db,"idiomas",["idioma","nivel"],$id_foreing,$id_value);
    //REFERENCIAS :
    $referencias = $conexion->get_array_of_arrays($db,"referencias",["nombre","empresa","puesto","telefono","email"],$id_foreing,$id_value);

    //Creación del CV
    require_once 'clases/cv_class.php';//importamos la clase de curriculum:
    //creamos un cv a partir de los datos obtenidos
    $cv_show = new Cv('plantillas/cv','style_sheets/curriculum_style.css',$nombre_completo,$objetivo_profesional,$foto_perfil,$contacto,$educacion,$habilidades,$sobre_mi,$experiencia_laboral,$certificaciones,$idiomas,$referencias);
}catch(Exception $e){//en caos de fallar, mostramos un mensaje sobre el fallo.
    echo "Fallo: ". $e;
}



//Nota: lo siguiente es el código que implementé para el caso de importar los datos desde el archivo curriculum.php, solo lo dejo comentado, pero se puede probar quitando los comentarios
/*
//importación de clase Cv y de la información de curriculum
require_once 'C:\xampp\htdocs\cv_php\cv_data\curriculum.php';
require_once 'C:\xampp\htdocs\cv_php\clases\cv_class.php';
//creación de instancia de Cv a partir de la información
if(isset($nombre_completo)&&isset($fecha_nacimiento)&&isset($direccion)&&isset($telefono)&&
isset($email)&&isset($foto_perfil)&&isset($objetivo_profesional)&&isset($educacion)&&isset($sobre_mi)&&
isset($experiencia_laboral)&&isset($habilidades)&&isset($certificaciones)&&isset($idiomas)&&
isset($referencias)){//comprobamos que todas las variables estén definidas
    //en caso afirmativo, creamos el array de contacto, y luego creamos un objeto Cv con las variables
    $contacto = [$fecha_nacimiento,$direccion,$telefono,$email];
    $cv_show = new Cv('plantillas\cv','style_sheets\curriculum_style.css',$nombre_completo,$objetivo_profesional,$foto_perfil,$contacto,$educacion,$habilidades,$sobre_mi,$experiencia_laboral,$certificaciones,$idiomas,$referencias);
}else{ //en caso contrario creamos un Cv de error con un mensaje informativo:
    $cv_show = Cv::create_error_Cv('plantillas\cv','style_sheets\curriculum_style.css',"Faltan variables por definir en el curriculum");
}
    */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="curriculum vitae dinámico"> <!-- descripción de la pagina -->
    <title>Curriculum vitae de <?= $cv_show->full_name?></title> <!-- título -->
    <link rel="stylesheet" href="style_sheets/global_style.css"><!-- Hoja de estilos de la página -->
</head>
<body><!-- uso de metodología BEM -->
    <!-- Decidí separa cada parte del CV con section y con nombre de clase alusivo a la información que contiene, luego los englobé en dos contenedores llamados rigth/left content para poder maquetar fácilmente como en el ejemplo -->
    <?php $cv_show->render();?><!-- Directamente usamos el método render sobre el objeto para mostrarlo en pantalla -->
</body>
</html>