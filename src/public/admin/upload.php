<?php
require_once __DIR__.'/libs/Config.php';
require_once __DIR__.'/libs/MediaManager.php';
require_once __DIR__.'/libs/functions.php';

use stcms\Config;
use stcms\MediaManager;

// Initialize timezone from config
Config::initTimezone();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    try {
        $filename = MediaManager::upload($_FILES['image']);
        $success = 'Image uploaded successfully: ' . $filename;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Redirect back with message
$redirectUrl = './upload-form.php';
if ($error) {
    $redirectUrl .= '?error=' . urlencode($error);
} elseif ($success) {
    $redirectUrl .= '?success=' . urlencode($success);
}

header('Location: ' . $redirectUrl);
exit;
