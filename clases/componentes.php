<?php 
declare(strict_types=1);
abstract class PageComponent{
    //Cada componente de la página necesita dos cosas para funcionar:
    public function __construct(
        public string $templates_route, //la ruta a sus plantillas
        public string $style_sheets_route //la ruta a su hoja de estilos
    )
    {}
    abstract protected function render(); //además de utilizar un método abstracto que renderizará el componente
}
?>