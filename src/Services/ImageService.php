<?php

namespace App\Services;

class ImageService
{
    /**
     * Optimiza y convierte una imagen a WebP
     * @param array $file Archivo de $_FILES
     * @param string $destinationDir Directorio de destino
     * @param int $maxWidth Ancho máximo (default 1920)
     * @param int $quality Calidad WebP (0-100)
     * @return string|false Nombre del archivo guardado o false
     */
    public static function optimizeAndSave($file, $destinationDir, $maxWidth = 1920, $quality = 80)
    {
        // 1. Validar Tipo
        $info = getimagesize($file['tmp_name']);
        if ($info === false)
            return false;

        $mime = $info['mime'];
        $srcImage = null;

        switch ($mime) {
            case 'image/jpeg':
                $srcImage = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $srcImage = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/webp':
                $srcImage = imagecreatefromwebp($file['tmp_name']);
                break;
            default:
                return false; // Formato no soportado
        }

        if (!$srcImage)
            return false;

        // 2. Calcular Nuevas Dimensiones (Resize)
        $width = imagesx($srcImage);
        $height = imagesy($srcImage);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = intval($height * ($maxWidth / $width));

            $dstImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preservar transparencia para PNG/WebP
            if ($mime == 'image/png' || $mime == 'image/webp') {
                imagealphablending($dstImage, false);
                imagesavealpha($dstImage, true);
            }

            imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($srcImage);
            $srcImage = $dstImage; // Reemplazar con la version redimensionada
        }

        // 3. Generar Nombre y Guardar como WebP
        $fileName = 'img_' . uniqid() . '.webp';
        $fullPath = rtrim($destinationDir, '/') . '/' . $fileName;

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        // Convertir palette images (PNG8) a truecolor para evitar conflictos con WebP
        if (!imageistruecolor($srcImage)) {
            imagepalettetotruecolor($srcImage);
        }

        $result = imagewebp($srcImage, $fullPath, $quality);
        imagedestroy($srcImage);

        return $result ? $fileName : false;
    }
}
