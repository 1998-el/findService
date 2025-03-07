<?php
class ImageProcessor {
    private const MAX_WIDTH = 800;
    private const MAX_HEIGHT = 800;
    private const QUALITY = 90;

    public function processUpload(array $file, string $uploadDir, int $userId): string {
        $this->validateFile($file);
        
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = 'worker_' . $userId . '_' . uniqid() . '.' . $fileExtension;
        $filePath = $uploadDir . '/' . $fileName;

        $resizedImage = $this->resizeImage($file['tmp_name']);
        $this->saveImage($resizedImage, $filePath, $file['type']);

        return $fileName;
    }

    private function validateFile(array $file): void {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Erreur lors du téléversement du fichier.');
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Type de fichier non supporté.');
        }
    }

    private function resizeImage(string $filePath) {
        list($width, $height, $type) = getimagesize($filePath);

        $ratio = $width / $height;
        $newWidth = min(self::MAX_WIDTH, $width);
        $newHeight = min(self::MAX_HEIGHT, $height);

        if ($newWidth / $newHeight > $ratio) {
            $newWidth = $newHeight * $ratio;
        } else {
            $newHeight = $newWidth / $ratio;
        }

        $src = imagecreatefromstring(file_get_contents($filePath));
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($src);

        return $dst;
    }

    private function saveImage($image, string $filePath, string $mimeType): void {
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($image, $filePath, self::QUALITY);
                break;
            case 'image/png':
                imagepng($image, $filePath, 9);
                break;
            case 'image/gif':
                imagegif($image, $filePath);
                break;
            default:
                throw new Exception('Type de fichier non supporté.');
        }

        imagedestroy($image);
    }
}