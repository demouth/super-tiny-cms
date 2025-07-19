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

<header class="navbar bg-dark text-white sticky-top shadow-sm">
    <div class="container-xl">
        <a href="./" class="navbar-brand mb-0 h1 text-white"><?php echo _h(_t('admin')) ?></a>
    </div>
</header>

<div class="container-xl mt-5">

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center mb-4">
                <h2 class="text-secondary mb-3"><?php echo _h(_t('choose_a_schema')) ?></h2>
                <p class="text-muted">Select a schema to manage your content</p>
            </div>

            <div class="list-group shadow-sm">

<?php
    foreach($schemas->getAll() as $schema) {
?>

                <a href="./records.php?schema=<?php echo _h($schema->name()) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <span class="fw-medium"><?php echo _h($schema->name()) ?></span>
                    <i class="bi bi-arrow-right text-muted"></i>
                </a>

<?php
    }
?>

            </div>
        </div>
    </div>
</div>

</body>
</html>
