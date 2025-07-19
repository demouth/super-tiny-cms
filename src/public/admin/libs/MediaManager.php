<?php
namespace stcms;

require_once __DIR__.'/Config.php';

use RuntimeException;

class MediaManager
{
    public static function upload($file)
    {
        $config = Config::get('uploads');
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('File upload failed');
        }
        
        if ($file['size'] > $config['max_size']) {
            throw new RuntimeException('File size exceeds limit');
        }
        
        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $config['allowed_types'])) {
            throw new RuntimeException('Invalid file type');
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $secureFilename = hash('sha256', uniqid() . microtime(true)) . '.' . strtolower($extension);
        
        $uploadDir = $config['upload_dir'];
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new RuntimeException('Cannot create upload directory');
            }
        }
        
        $destination = $uploadDir . '/' . $secureFilename;
        
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Failed to move uploaded file');
        }
        
        return $secureFilename;
    }
    
    public static function getUploadedFiles()
    {
        $config = Config::get('uploads');
        $uploadDir = $config['upload_dir'];
        
        if (!is_dir($uploadDir)) {
            return array();
        }
        
        $files = array();
        $iterator = new \DirectoryIterator($uploadDir);
        
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || !$fileInfo->isFile()) {
                continue;
            }
            
            $filename = $fileInfo->getFilename();
            $filepath = $fileInfo->getPathname();
            
            if (getimagesize($filepath)) {
                $files[] = array(
                    'filename' => $filename,
                    'size' => $fileInfo->getSize(),
                    'modified' => $fileInfo->getMTime(),
                    'path' => $filepath,
                );
            }
        }
        
        usort($files, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return $files;
    }
    
    public static function deleteFile($filename)
    {
        $config = Config::get('uploads');
        $filepath = $config['upload_dir'] . '/' . basename($filename);
        
        if (!file_exists($filepath)) {
            return false;
        }
        
        return unlink($filepath);
    }
    
    public static function getPublicUrl($filename)
    {
        $config = Config::get('uploads');
        return $config['public_url_path'] . '/' . $filename;
    }
}