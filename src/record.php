<?php
require_once __DIR__.'/libs/Database.php';
require_once __DIR__.'/libs/RecordSet.php';
require_once __DIR__.'/libs/Record.php';
require_once __DIR__.'/libs/Schemas.php';
require_once __DIR__.'/libs/functions.php';

use stcms\Database;
use stcms\Record;
use stcms\Schema;
use stcms\Schemas;

$schemas = new Schemas();
$schema = filter_input(INPUT_GET, 'schema', FILTER_DEFAULT, ['options' => ['default'=>'']]);
if (!$schemas->exists($schema)) return;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default'=>-1]]);
$db = new Database($schema);
$rs = $db->get();

if ($id === -1) {
    // insert
    $r = Record::create();
    $rs->add($r);
} else {
    // update
    if (!$rs->exists($id)) return;
    $r = $rs->get($id);
    if ($r->deleted()) return;
}


$error = "";
if (filter_input(INPUT_POST, 'stcms--action', FILTER_DEFAULT, ['options' => ['default'=>'']]) === 'save') {
    foreach ($schemas->get($schema)->getAll() as $name => $type) {
        switch ($type) {
            case Schema::TYPE_TEXT:
                // fallthrough
            case Schema::TYPE_TEXTAREA:
                $val = filter_input(INPUT_POST, $name, FILTER_DEFAULT, ['options' => ['default'=>'']]);
                $r->set($name, $val);
                break;
            case Schema::TYPE_URL:
                $val = filter_input(INPUT_POST, $name, FILTER_VALIDATE_URL, ['options' => ['default'=>'']]);
                $r->set($name, $val);
                break;
            case Schema::TYPE_DATE:
                $val = filter_input(INPUT_POST, $name, FILTER_VALIDATE_REGEXP, ['options' => ['default'=>'', 'regexp'=>'/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/']]);
                $r->set($name, $val);
                break;
            defailt:
                break;
        }
    }
    $saved = true;
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
            <li class="breadcrumb-item"><a href="./records.php?schema=<?php echo _h($schema) ?>"><?php echo _h($schema) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">edit</li>
        </ol>
    </nav>


<?php if ($error) { ?>

    <div class="alert alert-danger" role="alert">

        Save Error : <?php echo _h($error) ?>

    </div>

<?php } ?>


    <div class="row">
        <div class="col">
            <form action="" method="post">

<?php
    foreach($schemas->get($schema)->getAll() as $name => $type) {
?>
                <div class="mb-3">
                    <label class="form-label">

                        <?php echo _h($name) ?>

                    </label>

    <?php if ($type === Schema::TYPE_TEXT) { ?>

                    <input type="text" class="form-control" name="<?php echo _h($name) ?>" value="<?php
                        if ($r->exists($name)) echo _h($r->get($name));
                    ?>" />

    <?php } else if($type === Schema::TYPE_TEXTAREA) { ?>

                    <textarea class="form-control" rows="3" name="<?php echo _h($name) ?>"><?php
                        if ($r->exists($name)) echo _h($r->get($name));
                    ?></textarea>

    <?php } else if($type === Schema::TYPE_URL) { ?>

                    <input type="url" class="form-control" name="<?php echo _h($name) ?>" value="<?php
                        if ($r->exists($name)) echo _h($r->get($name));
                    ?>" />

    <?php } else if($type === Schema::TYPE_DATE) { ?>

                    <input type="date" class="form-control" name="<?php echo _h($name) ?>" value="<?php
                        if ($r->exists($name)) echo _h($r->get($name));
                    ?>" />

    <?php } else { ?>
    <?php } ?>

                </div>

<?php
    }
?>

                <button type="submit" name="stcms--action" value="save" class="btn btn-primary my-5">Save</button>

            </form>
        </div>
    </div>

</div>

</body>
</html>
