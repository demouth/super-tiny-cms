<?php
require_once __DIR__.'/libs/Config.php';
require_once __DIR__.'/libs/MediaManager.php';
require_once __DIR__.'/libs/Schemas.php';
require_once __DIR__.'/libs/functions.php';

use stcms\Config;
use stcms\MediaManager;

// Initialize timezone from config
Config::initTimezone();
use stcms\Schemas;

$schemas = new Schemas();

$error = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_STRING) ?: '';
$success = filter_input(INPUT_GET, 'success', FILTER_SANITIZE_STRING) ?: '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $filename = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING);
        if ($filename && MediaManager::deleteFile($filename)) {
            $success = 'Image deleted successfully';
        } else {
            $error = 'Failed to delete image';
        }
    }
}

$uploadedFiles = MediaManager::getUploadedFiles();
?>
<html>
<head>
    <meta charset="UTF-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <style>
        /* CSS-only modal for image preview */
        .image-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            padding: 20px;
            box-sizing: border-box;
        }
        
        .image-modal:target {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .image-modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .image-modal img {
            display: block;
            max-width: 100%;
            max-height: 80vh;
            width: auto;
            height: auto;
        }
        
        .image-modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            color: #666;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            background: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .image-modal-close:hover {
            color: #000;
            text-decoration: none;
        }
        
        .image-preview {
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .image-preview:hover {
            transform: scale(1.05);
        }
    </style>
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
                    <a href="./upload-form.php" class="list-group-item list-group-item-action border-0">
                        <?php echo _h(_t('upload_image')) ?>
                    </a>
                    <a href="./media.php" class="list-group-item list-group-item-action border-0 active">
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
                <h3 class="text-secondary mb-0"><?php echo _h(_t('media_management')) ?></h3>
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


            <!-- Image Gallery -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?php echo _h(_t('uploaded_images')) ?></h5>
                    
                    <?php if (empty($uploadedFiles)) { ?>
                        <p class="text-muted"><?php echo _h(_t('no_images_uploaded')) ?></p>
                    <?php } else { ?>
                        <div class="row g-3">
                            <?php foreach($uploadedFiles as $file) { ?>
                                <div class="col-md-4 col-lg-3">
                                    <div class="card">
                                        <a href="#modal-<?php echo _h($file['filename']) ?>">
                                            <img src="<?php echo _h(MediaManager::getPublicUrl($file['filename'])) ?>" 
                                                 class="card-img-top image-preview" style="height: 200px; object-fit: cover;">
                                        </a>
                                        <div class="card-body p-2">
                                            <p class="card-text small mb-1">
                                                <strong><?php echo _h($file['filename']) ?></strong>
                                            </p>
                                            <p class="card-text small text-muted mb-2">
                                                <?php echo number_format($file['size'] / 1024, 1) ?> KB<br>
                                                <?php echo date('Y-m-d H:i', $file['modified']) ?>
                                            </p>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="filename" value="<?php echo _h($file['filename']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('<?php echo _h(_t('delete_image_confirm')) ?>')">
                                                    <?php echo _h(_t('delete')) ?>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Image Modal -->
                                <div id="modal-<?php echo _h($file['filename']) ?>" class="image-modal">
                                    <div class="image-modal-content">
                                        <a href="#" class="image-modal-close">&times;</a>
                                        <img src="<?php echo _h(MediaManager::getPublicUrl($file['filename'])) ?>" alt="<?php echo _h($file['filename']) ?>">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>