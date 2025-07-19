<?php
require_once __DIR__.'/libs/Schemas.php';
require_once __DIR__.'/libs/Config.php';
require_once __DIR__.'/libs/functions.php';

use stcms\Schemas;
use stcms\Config;

$schemas = new Schemas();
$uploadConfig = Config::get('uploads');

$error = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_STRING) ?: '';
$success = filter_input(INPUT_GET, 'success', FILTER_SANITIZE_STRING) ?: '';

// Convert MIME types to user-friendly format names
$formatMap = [
    'image/jpeg' => 'JPEG',
    'image/png' => 'PNG', 
    'image/gif' => 'GIF',
    'image/webp' => 'WebP'
];
$supportedFormats = array_map(function($mimeType) use ($formatMap) {
    return $formatMap[$mimeType] ?? strtoupper(explode('/', $mimeType)[1]);
}, $uploadConfig['allowed_types']);

$maxSizeMB = round($uploadConfig['max_size'] / (1024 * 1024), 1);
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
                           class="list-group-item list-group-item-action">
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
                    <a href="./upload-form.php" class="list-group-item list-group-item-action border-0 active">
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
                    <?php echo _h(_t('schemas')) ?>
                </button>
                <ul class="dropdown-menu w-100">
                    <?php foreach($schemas->getAll() as $s) { ?>
                        <li><a class="dropdown-item" 
                               href="./records.php?schema=<?php echo _h($s->name()) ?>"><?php echo _h($s->name()) ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-secondary mb-0"><?php echo _h(_t('upload_image')) ?></h3>
            </div>

            <?php if ($error) { ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo _h(_t('error')) ?>: <?php echo _h($error) ?>
                </div>
            <?php } ?>

            <?php if ($success) { ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo _h($success) ?>
                </div>
            <?php } ?>

            <!-- Upload Form -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo _h(_t('select_image')) ?></h5>
                    <p class="text-muted mb-3">
                        <?php echo _h(_t('supported_formats')) ?>: <?php echo _h(implode(', ', $supportedFormats)) ?><br>
                        <?php echo _h(_t('max_size')) ?>: <?php echo _h($maxSizeMB) ?>MB
                    </p>
                    
                    <form action="upload.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo _h(_t('upload')) ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>