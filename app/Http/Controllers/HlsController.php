<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class HlsController extends Controller
{
    public function stream($date, $name, $filename)
    {
        $path = storage_path("app/public/videos/$date/$name/" . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $mimeType = $this->getMimeType($filename);
        $fileSize = filesize($path);
        $start = 0;
        $end = $fileSize - 1;

        // Обработка диапазонных запросов (для ts-сегментов)
        if (isset($_SERVER['HTTP_RANGE'])) {
            preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
            $start = intval($matches[1]);
            $end = isset($matches[2]) ? intval($matches[2]) : ($fileSize - 1);

            if ($start >= $fileSize || $end >= $fileSize) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes */$fileSize");
                exit;
            }
        }

        header('Content-Type: ' . $mimeType);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Expose-Headers: Content-Length,Content-Range');
        header('Accept-Ranges: bytes');

        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 206 Partial Content');
            header("Content-Range: bytes $start-$end/$fileSize");
            header("Content-Length: " . ($end - $start + 1));
        } else {
            header("Content-Length: $fileSize");
        }

        // Оптимизированная потоковая передача файла
        $chunkSize = 1024 * 1024; // 1MB chunks
        $handle = fopen($path, 'rb');
        fseek($handle, $start);

        while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
            $remaining = $end - $pos + 1;
            $size = min($chunkSize, $remaining);
            echo fread($handle, $size);
            flush();
        }

        fclose($handle);
        exit;
    }

    private function getMimeType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'm3u8':
                return 'application/vnd.apple.mpegurl';
            case 'ts':
                return 'video/MP2T';
            case 'mp4':
                return 'video/mp4';
            case 'm4s':
                return 'video/iso.segment';
            default:
                return 'application/octet-stream';
        }
    }
}
