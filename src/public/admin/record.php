<?php
require_once __DIR__.'/libs/Database.php';
require_once __DIR__.'/libs/RecordSet.php';
require_once __DIR__.'/libs/Record.php';
require_once __DIR__.'/libs/Schemas.php';
require_once __DIR__.'/libs/MediaManager.php';
require_once __DIR__.'/libs/functions.php';

use stcms\Database;
use stcms\Record;
use stcms\Schema;
use stcms\Schemas;
use stcms\MediaManager;

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
            case Schema::TYPE_IMAGE:
                $val = filter_input(INPUT_POST, $name, FILTER_DEFAULT, ['options' => ['default'=>'']]);
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
            <div class="mb-4">
                <h3 class="text-secondary"><?php echo $id === -1 ? _h(_t('add_new_record')) : _h(_t('edit')) . ' Record' ?></h3>
                <p class="text-muted mb-0">Schema: <strong><?php echo _h($schema) ?></strong></p>
            </div>

            <?php if ($error) { ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo _h(_t('error')) ?>: <?php echo _h($error) ?>
                </div>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="" method="post">

<?php
    foreach($schemas->get($schema)->getAll() as $name => $type) {
?>
                <div class="mb-5">
                    <label class="form-label fs-5">

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

    <?php } else if($type === Schema::TYPE_IMAGE) { ?>

                    <?php
                    $uploadedImages = MediaManager::getUploadedFiles();
                    $currentImage = $r->exists($name) ? $r->get($name) : '';
                    ?>

                    <input type="hidden" name="<?php echo _h($name) ?>" id="image-field-<?php echo _h($name) ?>" value="<?php echo _h($currentImage) ?>">

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="d-block text-muted fw-light"><?php echo _h(_t('selected_image')) ?>:</small>
                                <div id="selected-name-<?php echo _h($name) ?>" class="">
                                    <?php echo $currentImage ? _h($currentImage) : _h(_t('no_image_selected')) ?>
                                </div>
                            </div>

                            <?php if ($currentImage) { ?>
                                <div id="current-preview-<?php echo _h($name) ?>" class="mb-2">
                                    <img src="<?php echo _h(MediaManager::getPublicUrl($currentImage)) ?>"
                                        alt="Selected image"
                                        class="img-thumbnail"
                                        style="max-width: 200px; max-height: 150px;">
                                </div>
                            <?php } ?>

                            <div id="selected-preview-<?php echo _h($name) ?>" class="mb-2" style="display: none;">
                                <img src="" alt="Selected image" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                            </div>

                            <button type="button" class="btn btn-outline-secondary btn-sm me-2" id="clear-btn-<?php echo _h($name) ?>" onclick="clearImageSelection('<?php echo _h($name) ?>')" <?php echo !$currentImage ? 'style="display: none;"' : '' ?>>
                                <?php echo _h(_t('clear_selection')) ?>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="choose-btn-<?php echo _h($name) ?>" onclick="toggleImageGallery('<?php echo _h($name) ?>')" <?php echo $currentImage ? 'style="display: none;"' : '' ?>>
                                <?php echo _h(_t('choose_image')) ?>
                            </button>
                        </div>
                    </div>

                    <div id="image-gallery-<?php echo _h($name) ?>" class="mt-3" style="display: none;">
                        <?php if (count($uploadedImages) > 0) { ?>
                            <div class="border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0"><?php echo _h(_t('select_from_uploaded_images')) ?></h6>
                                    <button type="button" class="btn-close" onclick="toggleImageGallery('<?php echo _h($name) ?>')"></button>
                                </div>
                                <div class="row g-2">
                                    <?php foreach ($uploadedImages as $image) { ?>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="card h-100 image-option <?php echo $currentImage === $image['filename'] ? 'border-primary' : '' ?>"
                                                 style="cursor: pointer;"
                                                 onclick="selectImage('<?php echo _h($name) ?>', '<?php echo _h($image['filename']) ?>')">
                                                <img src="<?php echo _h(MediaManager::getPublicUrl($image['filename'])) ?>"
                                                     class="card-img-top"
                                                     style="height: 120px; object-fit: cover;"
                                                     alt="<?php echo _h($image['filename']) ?>">
                                                <div class="card-body p-2">
                                                    <small class="text-muted text-truncate d-block" title="<?php echo _h($image['filename']) ?>">
                                                        <?php echo _h(substr($image['filename'], 0, 16)) ?>...
                                                    </small>
                                                    <small class="text-muted">
                                                        <?php echo round($image['size'] / 1024, 1) ?> KB
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-start">
                                    <span><?php echo _h(_t('no_images_uploaded')) ?></span>
                                    <button type="button" class="btn-close" onclick="toggleImageGallery('<?php echo _h($name) ?>')"></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="form-text mt-2">
                        <?php echo _h(_t('upload_images_from_media')) ?>
                        <a href="./upload-form.php" target="_blank"><?php echo _h(_t('media_management')) ?></a>
                    </div>

    <?php } else { ?>
    <?php } ?>

                </div>

<?php
    }
?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="./records.php?schema=<?php echo _h($schema) ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" name="stcms--action" value="save" class="btn btn-primary"><?php echo _h(_t('save')) ?></button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Image selection functionality
function selectImage(fieldName, filename) {
    // Set value to hidden field
    const hiddenField = document.getElementById('image-field-' + fieldName);
    if (hiddenField) {
        hiddenField.value = filename;
    }

    // Display selected image name
    const selectedNameSpan = document.getElementById('selected-name-' + fieldName);
    if (selectedNameSpan) {
        selectedNameSpan.textContent = filename;
    }

    // Update preview image
    const currentPreview = document.getElementById('current-preview-' + fieldName);
    const selectedPreview = document.getElementById('selected-preview-' + fieldName);
    const selectedImg = selectedPreview ? selectedPreview.querySelector('img') : null;

    if (selectedImg) {
        selectedImg.src = '<?php echo rtrim(MediaManager::getPublicUrl(''), '/') ?>/' + filename;
        selectedPreview.style.display = 'block';
    }

    if (currentPreview) {
        currentPreview.style.display = 'none';
    }

    // Reset all image card selection states
    const allCards = document.querySelectorAll('.image-option');
    allCards.forEach(card => {
        card.classList.remove('border-primary');
    });

    // Set clicked card as selected
    event.target.closest('.image-option').classList.add('border-primary');

    // Show clear button and hide choose button
    const clearBtn = document.getElementById('clear-btn-' + fieldName);
    const chooseBtn = document.getElementById('choose-btn-' + fieldName);
    if (clearBtn) {
        clearBtn.style.display = 'inline-block';
    }
    if (chooseBtn) {
        chooseBtn.style.display = 'none';
    }

    // Close gallery after image selection
    toggleImageGallery(fieldName);
}

function toggleImageGallery(fieldName) {
    const gallery = document.getElementById('image-gallery-' + fieldName);
    if (gallery) {
        if (gallery.style.display === 'none') {
            gallery.style.display = 'block';
        } else {
            gallery.style.display = 'none';
        }
    }
}

function clearImageSelection(fieldName) {
    // Clear hidden field
    const hiddenField = document.getElementById('image-field-' + fieldName);
    if (hiddenField) {
        hiddenField.value = '';
    }

    // Clear selected image name
    const selectedNameSpan = document.getElementById('selected-name-' + fieldName);
    if (selectedNameSpan) {
        selectedNameSpan.textContent = '<?php echo _t('no_image_selected') ?>';
    }

    // Hide previews
    const currentPreview = document.getElementById('current-preview-' + fieldName);
    const selectedPreview = document.getElementById('selected-preview-' + fieldName);

    if (currentPreview) {
        currentPreview.style.display = 'none';
    }
    if (selectedPreview) {
        selectedPreview.style.display = 'none';
    }

    // Hide clear button and show choose button
    const clearBtn = document.getElementById('clear-btn-' + fieldName);
    const chooseBtn = document.getElementById('choose-btn-' + fieldName);
    if (clearBtn) {
        clearBtn.style.display = 'none';
    }
    if (chooseBtn) {
        chooseBtn.style.display = 'inline-block';
    }

    // Reset all image card selection states
    const allCards = document.querySelectorAll('.image-option');
    allCards.forEach(card => {
        card.classList.remove('border-primary');
    });
}
</script>

</body>
</html>
