<?php
require_once __DIR__.'/libs/Schemas.php';
require_once __DIR__.'/libs/functions.php';

use stcms\Schemas;

$schemas = new Schemas();
?>
<html>
<head>
    <meta charset="UTF-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</head>
<body>

<header class="navbar bg-light sticky-top shadow">
    <div class="container-xl">
        <a href="./" class="navbar-brand mb-0 h1"><?php echo _h(_t('admin')) ?></a>
    </div>
</header>

<div class="container-xl mt-4">

    <p>
        <?php echo _h(_t('choose_a_schema')) ?>
    </p>


    <ul>

<?php
    foreach($schemas->getAll() as $schema) {
?>

        <li>
            <a href="./records.php?schema=<?php echo _h($schema->name()) ?>"><?php echo _h($schema->name()) ?></a>
        </li>

<?php
    }
?>

    </ul>
</div>

</body>
</html>
