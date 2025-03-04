<section class="cv__left-content">
    <!-- Este contenedor tiene el contacto, la educación, habilidades y los idiomas -->
    <section class="left-content__content-section"> 
        <!-- En esta sección todas las entradas irán en un p y se resaltará la información con un em-->
        <p class="content-section__title">Contacto</p>
        <p class="content-section__line">📅  <em><?=$contacto[0]?></em></p>
        <p class="content-section__line">🗺️  <em><?=$contacto[1]?></em></p>
        <a class="content-section__line" href=<?="tel:".$contacto[2]?>>📞  <em><?=$contacto[2]?></em></a>
        <a class="content-section__line" href=<?="mailto:".$contacto[3]?> >✉️  <em><?=$contacto[3]?></em></a>
        <hr>
    </section>
    <section class="left-content__content-section"><!-- Esta sección aplica un forEach al arreglo de educación a fin de abarcar todas las posibles entradas. -->
        <p class="content-section__title">Educación</p>
        <ul class="content-section__list">
            <?php foreach($educacion as $entry): ?><!-- Para cada entrada, añade un li con la información necesaria -->
                <li><?= $entry["titulo"];?> <p><?= $entry["institucion"];?> </p> <p><?= $entry["fecha_inicio"];?> a <?= $entry["fecha_fin"];?></p> <p><?= $entry["descripcion"];?></p></li>
            <?php endforeach;?>
        </ul>
        <hr>
    </section>
    <section class="left-content__content-section">
        <p class="content-section__title">Habilidades</p>
            <ul class="content-section__list">
            <?php foreach($habilidades as $key=>$entry): ?><!-- Similar a los casos previos, salvo que ahora tomamos también la key junto con el elemento para mostrar la key -->
                <?php if(count($entry)==1):?> 
                    <!-- En caso de tener solo un elemento, lo muestra como tal -->
                    <li><?= $key;?>: <em><?=$entry[0];?></em></li>
                <?php else:?>
                    <!-- En caso de 2 o más, simplemente une las primero con una , y el último con un y -->
                    <li><?= $key;?>: <em><?= implode(", ",array_slice($entry,0,array_key_last($entry)))." y ".end($entry)?></em>.</li>    
                <?php endif;?>
            <?php endforeach;?>
        </ul>
        <hr>
    </section>
</section>