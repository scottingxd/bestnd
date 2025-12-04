<?php
namespace App\Helpers;

class UploadHelper
{
    public static function uploadProof(array $file, int $raffleId): array
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'message' => 'Parâmetros inválidos.', 'path' => null];
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'message' => 'Arquivo muito grande.', 'path' => null];
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'message' => 'Nenhum arquivo enviado.', 'path' => null];
            default:
                return ['success' => false, 'message' => 'Erro no upload.', 'path' => null];
        }
        
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return ['success' => false, 'message' => 'Arquivo excede 2MB.', 'path' => null];
        }
        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, ALLOWED_MIME_TYPES, true)) {
            return ['success' => false, 'message' => 'Tipo não permitido. Use JPG, PNG ou WEBP.', 'path' => null];
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, ALLOWED_EXTENSIONS, true)) {
            return ['success' => false, 'message' => 'Extensão não permitida.', 'path' => null];
        }
        
        $uniqueName = uniqid('proof_', true) . '_' . time() . '.' . $extension;
        $uniqueName = basename($uniqueName);
        
        $uploadDir = UPLOAD_PATH . '/proofs/' . $raffleId;
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return ['success' => false, 'message' => 'Erro ao criar diretório.', 'path' => null];
            }
        }
        
        $uploadPath = $uploadDir . '/' . $uniqueName;
        
        $reprocessed = self::reprocessImage($file['tmp_name'], $uploadPath, $mimeType);
        
        if (!$reprocessed) {
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return ['success' => false, 'message' => 'Erro ao mover arquivo.', 'path' => null];
            }
        }
        
        $relativePath = 'proofs/' . $raffleId . '/' . $uniqueName;
        
        return ['success' => true, 'message' => 'Upload realizado.', 'path' => $relativePath];
    }
    
    private static function reprocessImage(string $sourcePath, string $destPath, string $mimeType): bool
    {
        try {
            $image = null;
            
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = @imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($sourcePath);
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($sourcePath);
                    break;
            }
            
            if ($image === false || $image === null) {
                return false;
            }
            
            $saved = false;
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $saved = imagejpeg($image, $destPath, 90);
                    break;
                case 'image/png':
                    $saved = imagepng($image, $destPath, 8);
                    break;
                case 'image/webp':
                    $saved = imagewebp($image, $destPath, 90);
                    break;
            }
            
            imagedestroy($image);
            return $saved;
            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public static function deleteProof(string $relativePath): bool
    {
        $fullPath = UPLOAD_PATH . '/' . $relativePath;
        $realPath = realpath($fullPath);
        $uploadRealPath = realpath(UPLOAD_PATH);
        
        if ($realPath === false || strpos($realPath, $uploadRealPath) !== 0) {
            return false;
        }
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    public static function deleteRaffleProofs(int $raffleId): bool
    {
        $dir = UPLOAD_PATH . '/proofs/' . $raffleId;
        
        if (!is_dir($dir)) {
            return true;
        }
        
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return rmdir($dir);
    }
}