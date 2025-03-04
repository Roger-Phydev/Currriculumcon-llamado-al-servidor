<?php
//creo esta clase para las conexiones
declare(strict_types=1);
class ServerConection{
    public function __construct(//como parámetros tiene el usuario, contraseña(en privado)y host
        public string $host,
        public string $user,
        private string $password
    )
    {}
    public function query_data_base(string $data_base_name,string $query){
        //este método hace una consulta a la base de datos señalada con cierta consulta
        $conection = new mysqli($this->host,$this->user,$this->password,$data_base_name);//establece la conexión
        if($conection->connect_errno){//si hay un fallo:
            throw new ErrorException("Fallo en la conexión: $conection->connect_errno"); //lanza error informando
        } else{
            return $conection->query($query)->fetch_all(MYSQLI_ASSOC);//en caso contrario, retorna un array asociativo con la información obtenida
        }
    }
    public function get_array_of_arrays(string $data_base_name,string $table,array $keys,string $id_name="",string $id_value=""){
        //este método convierte lo obtenido por la llamada al servidor en un array de arrays del formato Cv
        $fields = implode(", ",$keys);//juntamos los campos
        if($id_name&&$id_value){//creamos una consulta según se tenga o no una id para filtrar
            $query = "SELECT $fields FROM $table WHERE $id_name=$id_value";
        }else{
            $query = "SELECT $fields FROM $table";
        }
        $results = $this->query_data_base($data_base_name,$query);//obtenemos los resultados
        if(empty($results)){
            return [];//en caso de resultado vacío, devuelve un array vacío.
        }
        $array_of_arrays=[]; //creamos una variable que devolveremos con el formato adecuado:
        foreach($results as $array){//para cada array dentro del array:
            $array_of_arrays[]=$array;
        }
        return $array_of_arrays;//devolvemos el array construido
    }
    public function get_asociative_array_of_arrays(string $data_base_name,string $table,string $sort_field,string $value_field,string $id_name="",$id_value=""){
        //este método convierte lo obtenido por la llamada al servidor en un array asociativo del formato Cv
        if($id_name&&$id_value){//creamos una consulta según se tenga o no una id para filtrar
            $query = "SELECT $sort_field, $value_field FROM $table WHERE $id_name=$id_value";
        }else{
            $query = "SELECT $sort_field, $value_field FROM $table";
        }
        $results = $this->query_data_base($data_base_name,$query);
        if(empty($results)){
            return [];//en caso de resultados vacíos, devuelve el array vacío
        }
        $associative_array_of_arrays = [];//creamos la variable que guardará el resultado
        foreach($results as $array){//para cada array obtenido:
            if(!array_key_exists($array[$sort_field],$associative_array_of_arrays)||!in_array($array[$value_field],$associative_array_of_arrays[$array[$sort_field]])){//si la key del campo que ordena no existe en el array asociativo, o si el valor asociado al array no está en el array asociativo (en su respectiva categoría)...
                $associative_array_of_arrays[$array[$sort_field]][]=$array[$value_field];//agrega el valor
            }
        }
        return $associative_array_of_arrays;//retornamos el valor    
    }
    public function get_and_transform_to_array(string $data_base_name,string $table,array $fields,string $id_name = "",string $id_value=""){
        //esta función consulta en una base de datos ciertos campos y selecciona solo cierto id, recibiendo el nombre del id y el valor que se busca en específico
        $query_fields = implode(", ",$fields);//unimos los campos
        if($id_name&&$id_value){
            $query = "SELECT $query_fields FROM $table WHERE $id_name=$id_value";//en caso de haber id, añadimos la condición a la búsqueda
        }else{
            $query = "SELECT $query_fields FROM $table"; //en caso que no haya id, simplemente se buscan los campos
        }
        $results = $this->query_data_base($data_base_name,$query);//así, directamente se obtienen los resultados:
        if(empty($results)){
            return [];//en caso de resultados vacíos, devuelve el array vacío
        }
        $array = [];//este será el array que devolveremos
        if(count($results)==1){//si solo devuelve un array
            foreach($fields as $field){//para cada campo
                $array[]=$results[0][$field];//agregamos el valor del resultado asociado al array
            }
            return $array; //y lo devolvemos
        }
        foreach($results as $result){
            $add=[];//creamos un array por cada resultado
            foreach($fields as $field){
                $add[]=$result[$field];//agregamos los valores de cada campo al array
            }
            $array[]=$add;//y agregamos el array de valores al que devolveremos
        }
        return $array;//devolvemos el array resultante.
    }
    public function get_one_value(string $data_base_name,string $table,string $field,string $id_name,string $id_value) {
        $query = "SELECT $field FROM $table WHERE $id_name=$id_value";
        $result = $this->query_data_base($data_base_name,$query);
        if(empty($result)){//en caso de que la consulta sea vacía
            return "";//devulve string vacío
        }
        return $result[0][$field];//en caso contrario, retorna el valor del campo solicitado
    }
}
?>