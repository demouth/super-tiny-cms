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

<header class="navbar bg-dark text-white sticky-top shadow-sm">
    <div class="container-xl">
        <a href="./" class="navbar-brand mb-0 h1 text-white"><?php echo _h(_t('admin')) ?></a>
    </div>
</header>

<div class="container-xl mt-4">

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block d-none">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-secondary"><?php echo _h(_t('schemas')) ?></h6>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach($schemas->getAll() as $s) { ?>
                        <a href="./records.php?schema=<?php echo _h($s->name()) ?>" 
                           class="list-group-item list-group-item-action <?php echo $s->name() === $schema ? 'active' : '' ?>">
                            <?php echo _h($s->name()) ?>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-semibold text-secondary"><?php echo _h(_t('media_management')) ?></h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="./upload-form.php" class="list-group-item list-group-item-action border-0">
                        <?php echo _h(_t('upload_image')) ?>
                    </a>
                    <a href="./media.php" class="list-group-item list-group-item-action border-0">
                        <?php echo _h(_t('uploaded_images')) ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Schema Selector -->
        <div class="col-12 d-md-none mb-3">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                    <?php echo _h(_t('current')) ?>: <?php echo _h($schema) ?>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-secondary mb-0"><?php echo _h($schema) ?></h3>
                <a href="./record.php?schema=<?php echo _h($schema) ?>" class="btn btn-primary"><?php echo _h(_t('add_new_record')) ?></a>
            </div>

            <?php if ($error) { ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo _h(_t('error')) ?>: <?php echo _h($error) ?>
                </div>
            <?php } ?>

            <div class="card shadow-sm">
                <table class="table table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th scope="col" style="width: 80px;">#</th>
                <th scope="col"><?php echo _h(_t('content')) ?></th>
                <th scope="col" style="width: 200px;"><?php echo _h(_t('last_modified')) ?></th>
                <th scope="col" style="width: 150px;"><?php echo _h(_t('actions')) ?></th>
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
                <td class="pt-3 pb-0">

                    <?php echo _h($id) ?>

                </td>
                <td class="pt-3 pb-0">

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
                <td class="pt-3 pb-0">
                    <p>
                        <?php echo _h(_t('created')) ?>:
                        <span class="text-muted">

                            <?php echo _h(date('Y-m-d H:i:s P', $r->createdAt())) ?>

                        </span>
                    </p>

                    <p>
                        <?php echo _h(_t('updated')) ?>:
                        <span class="text-muted">

                            <?php echo _h(date('Y-m-d H:i:s P', $r->updatedAt())) ?>

                        </span>
                    </p>
                </td>
                <td class="pt-3 pb-0">

                    <a href="./record.php?schema=<?php echo _h($schema) ?>&id=<?php echo _h($id) ?>" class="btn btn-secondary d-block mb-2"><?php echo _h(_t('edit')) ?></a>
                    <a href="./records.php?schema=<?php echo _h($schema) ?>&id=<?php echo _h($id) ?>&action=delete" onclick="return confirm('<?php echo _h(_t('delete_confirmation')) ?>')" class="btn btn-danger d-block"><?php echo _h(_t('delete')) ?></a>

                </td>
            </tr>

<?php
    }
?>

            </tbody>
        </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
