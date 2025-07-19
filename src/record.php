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
    <div class="container-xl">
        <a href="./" class="navbar-brand mb-0 h1">ADMIN</a>
    </div>
</header>

<div class="container-xl mt-4">

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block d-none">
            <div class="bg-light p-3 rounded mb-4">
                <h6 class="mb-3">Schemas</h6>
                <div class="list-group list-group-flush">
                    <?php foreach($schemas->getAll() as $s) { ?>
                        <a href="./records.php?schema=<?php echo _h($s->name()) ?>" 
                           class="list-group-item list-group-item-action <?php echo $s->name() === $schema ? 'active' : '' ?>">
                            <?php echo _h($s->name()) ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- Mobile Schema Selector -->
        <div class="col-12 d-md-none mb-3">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                    Current: <?php echo _h($schema) ?>
                </button>
                <ul class="dropdown-menu w-100">
                    <?php foreach($schemas->getAll() as $s) { ?>
                        <li><a class="dropdown-item <?php echo $s->name() === $schema ? 'active' : '' ?>" 
                               href="./records.php?schema=<?php echo _h($s->name()) ?>"><?php echo _h($s->name()) ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <?php if ($error) { ?>
                <div class="alert alert-danger" role="alert">
                    Save Error : <?php echo _h($error) ?>
                </div>
            <?php } ?>

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
