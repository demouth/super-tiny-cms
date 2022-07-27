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
    <nav class="navbar">
        <div class="container-fluid">
            <a href="./" class="navbar-brand mb-0 h1">ADMIN</a>
        </div>
    </nav>
</header>

<div class="container">

    <nav aria-label="breadcrumb" class="navbar bg-light ps-3 my-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item active" aria-current="page">home</li>
        </ol>
    </nav>

    <p>
        choose schema
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
