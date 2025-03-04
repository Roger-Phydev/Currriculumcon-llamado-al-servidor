<!-- esta será la base del CV -->
<link rel="stylesheet" href="<?= $styles_route?>">
<article class="cv">
        <!-- Se hacen llamadas a otras plantillas -->
        <?php require_once $templates_route.'\photo.php';?>
        <?php require_once $templates_route.'\presentation.php';?>
        <?php require_once $templates_route.'\left_content.php';?>
        <?php require_once $templates_route.'\right_content.php';?>
</article>
