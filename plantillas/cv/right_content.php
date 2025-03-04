<section class="cv__right-content">
    <section class="right-content__content-section">
        <p class="content-section__title">Acerca de mi</p>
        <p class="content-section__description">
            <?= $sobre_mi ?>
        </p>
        <hr>
    </section>
    <section class="right-content__content-section">
        <p class="content-section__title">Experiencia laboral</p>
        <ul class="content-section__list content-section__list--line">
            <?php foreach($experiencia_laboral as $entry): ?>
                <!-- se crea un título de cada compañía y una lista de información detallada -->
                <li>
                    <p><?= $entry["puesto"];?></p>
                    <ul class="content-section__list content-section__list--features">
                        <li><em><?= $entry["empresa"];?></em></li>
                        <li><em><?= $entry["fecha_inicio"];?> a <?= $entry["fecha_fin"];?></em></li>
                        <li><em><?= $entry["responsabilidades"];?></em></li>
                    </ul>
                </li>
            <?php endforeach;?>
        </ul>
        <hr>
    </section>
    <section class="right-content__content-section">
        <p class="content-section__title">Certificaciones</p>
        <ul class="content-section__list content-section__list--normal">
            <?php foreach($certificaciones as $entry): ?><!-- de igual forma, se crea un parrafo por cada certificación con su detalle -->
                <li><?= $entry["nombre"];?> <?= $entry["institucion"];?> obtenido en la fecha: <?= $entry["fecha_obtencion"];?></li>
            <?php endforeach;?>
        </ul>
        <hr>
    </section>
    <section class="right-content__content-section">
        <p class="content-section__title">Idiomas</p>
        <ul class="content-section__list content-section__list--normal">
            <?php foreach($idiomas as $entry): ?><!-- En esta sección se crea un li con la información de idioma y nivel seguidos -->
                <li><?= $entry["idioma"];?>: <?= $entry["nivel"];?></li>
            <?php endforeach;?>
        </ul>
        <hr>
    </section>
    <section class="right-content__content-section">
        <p class="content-section__title">Referencias</p>
        <ul class="content-section__list content-section__list--line">
            <?php foreach($referencias as $entry): ?><!-- cada referencia será un título y luego una lista detallada de información al respecto -->
                <li>
                    <p><?= $entry["nombre"];?></p>
                    <ul class="content-section__list content-section__list--features">
                        <li><em><?= $entry["empresa"];?></em></li>
                        <li><em><?= $entry["puesto"];?></em></li>
                        <li><em><?= $entry["telefono"];?></em></li>
                        <li><em><?= $entry["email"];?></em></li>
                    </ul>
                </li>
            <?php endforeach;?>
        </ul>
        <hr>
    </section>
</section>