<?php 
require 'componentes.php';
class Cv extends PageComponent {
    public function __construct( //la implementación de la clase CV
        public string $templates_route,
        public string $style_sheets_route, //hereda estas dos de la clase PageComponent, además de...
        //las siguientes propiedades señalan que tipo se espera de ellas.
        public string $full_name, // nombre
        public string $presentation, //presentación
        public string $photo_route, //ruta de foto
        public array $contact,//contacto
        public array $education, //educación
        public array $skills, //habilidades
        public string $about_me, //sobre mí
        public array $working_experience, //experiencia laboral
        public array $certifications, //certificaciones
        public array $languages, //idiomas
        public array $references //referencias
    ){
        parent::__construct($templates_route,$style_sheets_route);
    }
    static private function validate_strings_array(array $an_array):bool{ //creamos un método estático para comprobas si un array está conformado solo de string no vacíos.
        return array_reduce($an_array,function ($carry,$actual){
            $carry = $carry && is_string($actual) && !empty($actual);
            return $carry;
        },true);
        //devuelve un reduce, que inicia con true y en cada caso conjunta el anterior con si es o no string la entrada actual, así como que no sea vacía. Al final, solo devolverá true si todas las entradas fueron strings no vacios, y false en caso contrario.
    }
    static private function validate_array_of_arrays(array $an_array):bool{
        //este método valida que un array esté formado por arrays
        return array_reduce($an_array,function ($carry,$actual){
            $carry = $carry && is_array($actual);
            return $carry;
        },true);
    }
    static private function validate_array_from_keys(array $an_array,array $keys):bool{
        //este método valida que un array tenga ciertas keys
        if(array_keys($an_array)!=$keys){//si sus keys no coinciden con las dichas, devuelve falso
            return false;
        }
        return true;//en caso de pasar, devuelve verdero
    }
    static private function validate_array_format(array $an_array,array $keys,string $type):bool{
        //este método sirve para validar el formato de los array
        if($type=="simple"){
            //en el caso simple es solo un array de strings
            return Cv::validate_strings_array($an_array);
        }
        if(!Cv::validate_array_of_arrays($an_array)){//en caso de no ser array de arrays para el resto de tipos
            return false;//retorna falso
        }
        if($type == "compuest"){//en el tipo compuesto
            //Se tratan de arrays de arrays, donde cada elemento es otro array con las keys solicitadas y sus valores son strings
            $validity = true;//inicializamos la variable en true
            foreach($an_array as $value){//para cada array interno
                $validity = $validity && Cv::validate_strings_array($value) && Cv::validate_array_from_keys($value,$keys);//verifica que tenga las keys proporcionadas y que además sea un array de strings
            }
            return $validity;//devuelve el resultado
        }else if($type == "asociative"){
            //en el caso asociativo, son arrays compuestos de arrays de strings, cuyas keys también son string no vacíos
            if(!Cv::validate_strings_array(array_keys($an_array))){//en caso que el array no tenga keys string no vacíos...
                return false; //devuelve falso
            }
            //en caso de si ser un array con keys string no vacíos, comprobamos que cada elemento sea un array de string no vacíos
            $validity = true; // para ello inicializamos en true esta variable
            foreach($an_array as $item){
                $validity = $validity && Cv::validate_strings_array($item);//actualizamos la variable para verificar que cada array interior este compuesto por strings no vacíos
            }
            return $validity;//retornamos el resultado de las verificaciones
        }
        return false;//en cualquier otro caso de tipo devolvemos falso
    }
    public function validate_cv_format():array{
        //en caso de estar vacías las rutas, simplemento lo señalamos
        if(empty($this->templates_route)){
            throw new ErrorException("Cuidado, no está definida una ruta a plantillas del cv");
        }else if(empty($this->style_sheets_route)){
            throw new ErrorException("Advertencia: ruta a hoja de estilos no definida");
        }
        $proof = [//creamos un array de prueba con todos los demás datos de forma asociativa
            "nombre_completo"=>$this->full_name,
            "objetivo_profesional"=>$this->presentation,
            "ruta_foto"=>$this->photo_route,
            "contacto"=>$this->contact,
            "educacion"=>$this->education,
            "habilidades"=>$this->skills,
            "sobre_mi"=>$this->about_me,
            "experiencia_laboral"=>$this->working_experience,
            "certificaciones"=>$this->certifications,
            "idiomas"=>$this->languages,
            "referencias"=>$this->references
        ];
        $message = "";//creamos una variable para el mensaje
        $keys = [
            "full_name","presentation","photo_route","contact","education","skills","about_me","working_experience","certifications","languages","references"
        ];//keys del objeto
        foreach($keys as $key){//recorremos el array de keys
            $message = "Variable vacía: $key";//escribimos el mensaje en caso de ser variable vacía
            $this->$key= $this->$key ? $this->$key ://si el valor no está vacío lo deja igual...
            match($key){//y rellena para que cumpla el formato en caso de estar vacío, pero informa del problema
                "full_name","presentation","photo_route","about_me"=>$message,
                "contact"=>[$message,$message,$message,$message],
                "education"=>[["institucion"=>$message,"titulo"=>$message,"fecha_inicio"=>$message,"fecha_fin"=>$message,"descripcion"=>$message]],
                "skills"=>[$message=>["?"]],
                "working_experience"=>[["puesto"=>$message,"empresa"=>$message,"fecha_inicio"=>$message,"fecha_fin"=>$message,"responsabilidades"=>$message]],
                "certification"=>[["nombre"=>$message,"institucion"=>$message,"fecha_obtencion"=>$message]],
                "languages"=>[["idioma"=>$message,"nivel"=>$message]],
                "references"=>[["nombre"=>$message,"empresa"=>$message,"puesto"=>$message,"telefono"=>$message,"email"=>$message]]
            };
        }
        //Comprobación de formato
        //Ahora vamos a comprobar el formato de cada dato, al menos hasta cierto nivel sin entrar en regex:
        //Por un lado, el nombre, presentacion , ruta de foto, y sobre mi los tomaremos como validos si simplemento no son vacíos. En este punto, ellos ya están aprobados. Ahora comprobaremos el resto de variables:
        $wrong_format = false; //creamos esta variable de manera análoga a la de empties
        //comprobación de contacto:
        if(!(Cv::validate_array_format($proof["contacto"],[],"simple")&&count($proof["contacto"])==4)){//en caso de no pasar la validación (sea array de strings con longitud 4)
            $message = "Error de formato en contacto: debe ser un array compuestos por strings no vacíos de fecha de nacimiento, dirección, teléfono e email. \n"; //escribimos un mensaje
            $wrong_format = true;//actualizamos el valor de la variable
        }
        //comprobación de educacion:
        else if(!Cv::validate_array_format($proof["educacion"],["institucion","titulo","fecha_inicio","fecha_fin","descripcion"],"compuest")){
            $wrong_format//preguntamos si el formato ya no se cumplió antes
            ?
            $message .= "Error de formato en educación: debe ser un array de arrays de strings no vacíos con las siguientes keys: institucion, titulo, fecha_inicio, fecha_fin y descripcion (sin acentos).\n" //en caso que sí, concatenamos el mensage
            :
            $message="Error de formato en educación: debe ser un array de arrays de strings no vacíos con las siguientes keys: institucion, titulo, fecha_inicio, fecha_fin y descripcion (sin acentos).\n";//en caso que apenas se rompió el formato, reasignamos el mensaje
            $wrong_format = true; //nos aseguramos que el valor de wrong format sea true
        }
        //comprobación de habilidades:
        else if(!Cv::validate_array_format($proof["habilidades"],[],"asociative")){
            $wrong_format//preguntamos si el formato ya no se cumplió antes
            ?
            $message .= "Error de formato en habilidades: debe ser un array de arrays de strings no vacíos, cuyas keys también deben ser strings.\n" //en caso que sí, concatenamos el mensage
            :
            $message="Error de formato en habilidades: debe ser un array de arrays de strings no vacíos, cuyas keys también deben ser strings.\n";//en caso que apenas se rompió el formato, reasignamos el mensaje
            $wrong_format = true; //nos aseguramos que el valor de wrong format sea true
        }
        //comprobación de experiencia laboral:
        else if(!Cv::validate_array_format($proof["experiencia_laboral"],["puesto","empresa","fecha_inicio","fecha_fin","responsabilidades"],"compuest")){
            $wrong_format//preguntamos si el formato ya no se cumplió antes
            ?
            $message .= "Error de formato en experiencia_laboral: debe ser un array de arrays de strings no vacíos con las siguientes keys: puesto, empresa, fecha_inicio, fecha_fin y responsabilidades.\n" //en caso que sí, concatenamos el mensage
            :
            $message="Error de formato en experiencia_laboral: debe ser un array de arrays de strings no vacíos con las siguientes keys: puesto, empresa, fecha_inicio, fecha_fin y responsabilidades.\n";//en caso que apenas se rompió el formato, reasignamos el mensaje
            $wrong_format = true; //nos aseguramos que el valor de wrong format sea true
        }
        //comprobación de certificaciones:
        else if(!Cv::validate_array_format($proof["certificaciones"],["nombre","institucion","fecha_obtencion"],"compuest")){
            $wrong_format//preguntamos si el formato ya no se cumplió antes
            ?
            $message .= "Error de formato en certificaciones: debe ser un array de arrays de strings no vacíos con las siguientes keys: nombre, institucion y fecha_obtencion (sin acentos).\n" //en caso que sí, concatenamos el mensage
            :
            $message="Error de formato en certificaciones: debe ser un array de arrays de strings no vacíos con las siguientes keys: nombre, institucion y fecha_obtencion (sin acentos).\n";//en caso que apenas se rompió el formato, reasignamos el mensaje
            $wrong_format = true; //nos aseguramos que el valor de wrong format sea true
        }
        //comprobación de idiomas:
        else if(!Cv::validate_array_format($proof["idiomas"],["idioma","nivel"],"compuest")){
            $wrong_format//preguntamos si el formato ya no se cumplió antes
            ?
            $message .= "Error de formato en idiomas: debe ser un array de arrays de strings no vacíos con las siguientes keys: idioma,nivel.\n" //en caso que sí, concatenamos el mensage
            :
            $message="Error de formato en idiomas: debe ser un array de arrays de strings no vacíos con las siguientes keys: idioma,nivel.\n";//en caso que apenas se rompió el formato, reasignamos el mensaje
            $wrong_format = true; //nos aseguramos que el valor de wrong format sea true
        }
        //comprobación de referencias:
        else if(!Cv::validate_array_format($proof["referencias"],["nombre","empresa","puesto","telefono","email"],"compuest")){
            $wrong_format//preguntamos si el formato ya no se cumplió antes
            ?
            $message .= "Error de formato en referencias: debe ser un array de arrays de strings no vacíos con las siguientes keys: nombre, empresa, puesto, telefono e email (sin acentos).\n" //en caso que sí, concatenamos el mensage
            :
            $message="Error de formato en referencias: debe ser un array de arrays de strings no vacíos con las siguientes keys: nombre, empresa, puesto, telefono e email (sin acentos).\n";//en caso que apenas se rompió el formato, reasignamos el mensaje
            $wrong_format = true; //nos aseguramos que el valor de wrong format sea true
        }
        //al final, veremos si hubo algun fallo en el formato:
        if($wrong_format){
            return [
                "correct"=>false,
                "message"=>$message
            ];
        }
        return [
            "correct"=>true,
            "message"=>""
        ];//en caso de aprobar todo, solo devolvemos un mensaje vacío
    }
    public function render(){ //implementación obligatoria del método abstracto "render"
        if($this->validate_cv_format()["correct"]){
            //en caso de tener el formato correcto:
            
            //asignamos valores de variables usadas en cada plantilla:
            //RUTAS DEL CUERPO BASE:
            $styles_route = $this->style_sheets_route;
            $templates_route = $this->templates_route;
            //PHOTO
            $foto_perfil = $this->photo_route;
            //PRESENTATION
            $nombre_completo = $this->full_name;
            $objetivo_profesional = $this->presentation;
            //LEFT CONTENT
            $contacto = $this->contact;
            $educacion = $this->education;
            $habilidades = $this->skills;
            //RIGHT CONTENT
            $sobre_mi = $this->about_me;
            $experiencia_laboral = $this->working_experience;
            $certificaciones = $this->certifications;
            $idiomas = $this->languages;
            $referencias = $this->references;
            //finalmente, llamamos a la plantilla central:
            require "$this->templates_route/cv.php";
        }else{//en caso que no cumpla con el formato:
            Cv::create_error_Cv($this->templates_route,$this->style_sheets_route,$this->validate_cv_format()["message"])->render();
            //creamos un cv de error con el mensaje previamente realizado y lo renderizamos
        }
    }
    static public function create_error_Cv($templates_route,$style_sheets_route,$message){
        //creamos un método estático para crear un CV de mensaje de error.
        return new self($templates_route,$style_sheets_route,"Error","!!!!!!!!!!!","?",
        ["?","?","?","?"],
        [[
            "institucion" => "?",
            "titulo" => "?",
            "fecha_inicio" => "?",
            "fecha_fin" => "?",
            "descripcion" => "?"
        ]],[
            "?"=>["?"]
        ],$message,[[
            "puesto" => "?",
            "empresa" => "?",
            "fecha_inicio" => "?",
            "fecha_fin" => "?",
            "responsabilidades" => "?"
        ]],[[
            "nombre" => "?",
            "institucion" => "?",
            "fecha_obtencion" => "?"
        ]],[[
            "idioma" => "?",
            "nivel" => "?"
        ]],[[
            "nombre" => "?",
            "empresa" => "?",
            "puesto" => "?",
            "telefono" => "?",
            "email" => "?"            
        ]]);
    }
}
?>