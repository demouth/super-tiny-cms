<?php
require_once __DIR__.'/libs/Database.php';
require_once __DIR__.'/libs/RecordSet.php';
require_once __DIR__.'/libs/Record.php';
require_once __DIR__.'/libs/Schemas.php';
require_once __DIR__.'/libs/functions.php';

use stcms\Database;
use stcms\Schemas;


$schemas = new Schemas();
$schema = filter_input(INPUT_GET, 'schema', FILTER_DEFAULT, ['options' => ['default'=>'']]);
if (!$schemas->exists($schema)) return;
$db = new Database($schema);
$rs = $db->get();

$error = '';
if (filter_input(INPUT_GET, 'action', FILTER_DEFAULT, ['options' => ['default'=>'']]) === 'delete') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default'=>0]]);
    if (!$rs->exists($id)) return;
    $r = $rs->get($id);
    if ($r->deleted()) return;
    $r->delete();

    try {
        $db->set($rs);
        header('Location: ./records.php?schema='.$schema);
        return;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
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
            <li class="breadcrumb-item"><a href="./">home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo _h($schema) ?></li>
        </ol>
    </nav>


<?php if ($error) { ?>

    <div class="alert alert-danger" role="alert">

        Save Error : <?php echo _h($error) ?>

    </div>

<?php } ?>

    <a href="./record.php?schema=<?php echo _h($schema) ?>" class="btn btn-primary mb-5">Add new record</a>


    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">data</th>
                <th scope="col">edited at</th>
                <th scope="col">action</th>
            </tr>
        </thead>
        <tbody>

<?php
    $sortedRs = $rs->getAll();
    krsort($sortedRs);
    foreach($sortedRs as $id => $r) {
        if ($r->deleted()) continue;
?>

            <tr>
                <td>

                    <?php echo _h($id) ?>

                </td>
                <td>

                    <?php
                        $keys = $r->keys();
                        foreach($keys as $key) {
                            if ($r->exists($key)) {
                    ?>

                                <p class="lh-1 mb-2">

                                    <?php echo _h($key) ?> :

                                    <span class="text-muted">

                                        <?php echo _h($r->get($key)) ?>

                                    </span>
                                </p>

                    <?php
                            }
                        }
                    ?>

                </td>
                <td>
                    <p>
                        created at:
                        <span class="text-muted">

                            <?php echo _h(date('Y-m-d H:i:s P', $r->createdAt())) ?>

                        </span>
                    </p>

                    <p>
                        updated at:
                        <span class="text-muted">

                            <?php echo _h(date('Y-m-d H:i:s P', $r->updatedAt())) ?>

                        </span>
                    </p>
                </td>
                <td>

                    <a href="./record.php?schema=<?php echo _h($schema) ?>&id=<?php echo _h($id) ?>" class="btn btn-secondary d-block mb-2">edit</a>
                    <a href="./records.php?schema=<?php echo _h($schema) ?>&id=<?php echo _h($id) ?>&action=delete" onclick="return confirm('Delete record. Are you sure you want to do this?')" class="btn btn-danger d-block">delete</a>

                </td>
            </tr>

<?php
    }
?>

        </tbody>
    </table>
</div>

</body>
</html>
