<?php

namespace App\Services;

use App\Models\User;
use App\Models\Video;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg as MainFFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoUploadService
{
    private const MAX_DURATION = 60;
    private const TARGET_WIDTH = 720;
    private const TARGET_HEIGHT = 1280;

    public function processUpload(
        UploadedFile $videoFile,
        int $fromSec,
        int $toSec,
        array $metadata,
        User $user
    ): array {
        $this->validateDuration($fromSec, $toSec);

        $uploadData = $this->prepareUploadPaths($videoFile);
        $this->storeOriginalVideo($videoFile, $uploadData['processing_path']);

        $processedData = $this->processVideoFile(
            $uploadData['processing_path'],
            $fromSec,
            $toSec,
            $uploadData
        );

        $video = $this->createVideoRecord($metadata, $processedData, $user);

        return [
            'video_id' => $video->id,
            'preview_url' => $processedData['preview_url'],
            'video_url' => $processedData['video_url']
        ];
    }

    private function validateDuration(int $fromSec, int $toSec): void
    {
        if (($toSec - $fromSec) > self::MAX_DURATION) {
            throw new \InvalidArgumentException(
                'Duration exceeds maximum allowed'
            );
        }
    }

    private function prepareUploadPaths(UploadedFile $file): array
    {
        $name = sha1(time());
        $date = date('d-m-Y');
        $extension = $file->getClientOriginalExtension();

        $basePath = "videos/$date/$name";
        Storage::disk('public')->makeDirectory($basePath);

        return [
            'name' => $name,
            'date' => $date,
            'extension' => $extension,
            'base_path' => $basePath,
            'processing_path' => "$basePath/{$name}_processing.$extension",
            'output_path' => "$basePath/$name.$extension",
            'hls_path' => "$basePath/$name.m3u8",
            'preview_path' => "$basePath/$name.webp"
        ];
    }

    private function storeOriginalVideo(UploadedFile $file, string $storagePath): void
    {
        $file->storeAs('', $storagePath, 'public');
    }

    private function processVideoFile(
        string $inputPath,
        int $fromSec,
        int $toSec,
        array $paths
    ): array {
        $this->trimAndResizeVideo($inputPath, $fromSec, $toSec, $paths['output_path']);
        $this->createHlsStream($paths['output_path'], $paths['hls_path']);
        $this->generatePreview($paths['output_path'], $paths['preview_path']);

        Storage::disk('public')->delete($inputPath);

        return [
            'video_url' => Storage::url($paths['hls_path']),
            'preview_url' => Storage::url($paths['preview_path'])
        ];
    }

    private function trimAndResizeVideo(string $inputPath, int $fromSec, int $toSec, string $outputPath): void
    {
        FFMpeg::fromDisk('public')
            ->open($inputPath)
            ->addFilter(new \FFMpeg\Filters\Video\CustomFilter("trim=start={$fromSec}:end={$toSec}"))
            ->addFilter(new \FFMpeg\Filters\Video\CustomFilter("scale=-1:" . self::TARGET_HEIGHT))
            ->addFilter(new \FFMpeg\Filters\Video\CustomFilter("crop=" . self::TARGET_WIDTH . ":" . self::TARGET_HEIGHT))
            ->export()
            ->toDisk('public')
            ->inFormat(new X264('aac'))
            ->save($outputPath);
    }

    private function createHlsStream(string $inputPath, string $outputPath): void
    {
        $highBitrate = (new X264('aac'))->setKiloBitrate(7000);

        FFMpeg::fromDisk('public')
            ->open($inputPath)
            ->exportForHLS()
            ->toDisk('public')
            ->addFormat($highBitrate)
            ->save($outputPath);
    }

    private function generatePreview(string $videoPath, string $previewPath): void
    {
        $ffmpeg = MainFFMpeg::create([
            'ffmpeg.binaries'  => 'C:\ffmpeg\bin\ffmpeg.exe',
            'ffprobe.binaries' => 'C:\ffmpeg\bin\ffprobe.exe'
        ]);

        $video = $ffmpeg->open(Storage::disk('public')->path($videoPath));
        $frame = $video->frame(TimeCode::fromSeconds(0));
        $frame->save(Storage::disk('public')->path($previewPath));
    }

    private function createVideoRecord(array $metadata, array $paths, User $user): Model
    {
        $slug = empty($metadata['title'])
            ? $user->username . '-' . ($user->videos()->count() + 1)
            : Str::slug($metadata['title']);

        return $user->videos()->create([
            'title' => $metadata['title'] ?? null,
            'description' => $metadata['description'] ?? null,
            'filename' => basename($paths['video_url']),
            'fullpath' => $paths['video_url'],
            'preview' => $paths['preview_url'],
            'slug' => $slug,
            'views' => 0
        ]);
    }
}
