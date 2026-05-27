<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ValidationException;

final class ImageService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    private const ALLOWED_MIME_TYPES = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    public function storeUploadedProductImage(?array $file, string $redirectTo): string
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new ValidationException('Veuillez téléverser une image JPEG ou PNG.', $redirectTo);
        }

        $originalName = (string) ($file['name'] ?? '');
        $tmpName = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);

        if ($size <= 0 || $size > 5 * 1024 * 1024) {
            throw new ValidationException('L’image doit peser au maximum 5 Mo.', $redirectTo);
        }

        $extension = mb_strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new ValidationException('Seuls les formats JPEG et PNG sont acceptés.', $redirectTo);
        }

        $mimeType = mime_content_type($tmpName);
        if ($mimeType !== self::ALLOWED_MIME_TYPES[$extension]) {
            throw new ValidationException('Le type réel du fichier ne correspond pas à son extension.', $redirectTo);
        }

        $directory = (string) config('app.product_upload_path');
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $targetFilename = bin2hex(random_bytes(16)) . '.jpg';
        $targetPath = $directory . '/' . $targetFilename;

        $this->resizeAndSaveAsJpeg($tmpName, $mimeType, $targetPath, 1200, 800);

        return rtrim((string) config('app.product_upload_url'), '/') . '/' . $targetFilename;
    }

    private function resizeAndSaveAsJpeg(string $tmpName, string $mimeType, string $targetPath, int $maxWidth, int $maxHeight): void
    {
        $source = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($tmpName),
            'image/png' => @imagecreatefrompng($tmpName),
            default => false,
        };

        if ($source === false) {
            throw new ValidationException('Impossible de traiter cette image.', '/produits/ajouter');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $ratio = min($maxWidth / max(1, $width), $maxHeight / max(1, $height), 1);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $target = imagecreatetruecolor($newWidth, $newHeight);
        $background = imagecolorallocate($target, 255, 255, 255);
        imagefill($target, 0, 0, $background);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagejpeg($target, $targetPath, 85);
        imagedestroy($source);
        imagedestroy($target);
    }
}
