<?php
ob_start();
require '../asset/module/header.php';

if($_SESSION['membre'] == '')
{
    header('location:' . URL . '/error/403');
}

?>
<section class="galerie">
    <div class="position-H1">
        <h1>Back-Office</h1>
    </div>
    <div class="content-bo">
        <ul>
            <div class="elm-bo">Gestion des membres</div>
            <li class="elm-bo"><a href="<?= URL ?>/admin/add-admin">Ajouter un administrateur</a></li>
            <li class="elm-bo"><a href="<?= URL ?>/admin/list-admin">Liste des administrateurs</a></li>
        </ul>
        <ul>
            <div class="elm-bo">Gestion des articles</div>
            <li class="elm-bo"><a href="<?= URL ?>/admin/add-art">Créer un nouvel article</a></li>
            <li class="elm-bo"><a href="<?= URL ?>/admin/modif-art">Modifier un article</a></li>
        </ul>
        <ul>
            <div class="elm-bo">Gestion des évenements</div>
            <li class="elm-bo"><a href="<?= URL ?>/admin/add-event">Ajouter un évenement</a></li>
            <li class="elm-bo"><a href="<?= URL ?>/admin/modif-event">Modifier un évenement</a></li>
        </ul>
        <ul>
            <div class="elm-bo">Gestion de la newsletter</div>
            <li class="elm-bo"><a href="<?= URL ?>/admin/list-newsletter">Liste  des abonnés</a></li>
        </ul>
    </div>
</section>
<?php
    require '../asset/module/footer.php';