<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AlertImageService
{
    public function upload(UploadedFile $file, ?Alert $alert = null, array $options = []): AlertImage
    {
        $path = $file->store('images/alerts', 'public');

        $data = [
            'path' => $path,
            'width' => $options['width'] ?? 720,
            'height' => $options['height'] ?? 960,
            'fit' => $options['fit'] ?? 'CONTAIN',
        ];

        if ($alert) {
            $data['alert_id'] = $alert->id;

            return AlertImage::create($data);
        }

        return new AlertImage($data);
    }

    public function update(AlertImage $alertImage, UploadedFile $file, array $options = []): AlertImage
    {
        if ($alertImage->path && Storage::disk('public')->exists($alertImage->path)) {
            Storage::disk('public')->delete($alertImage->path);
        }

        $path = $file->store('images/alerts', 'public');

        $alertImage->update([
            'path' => $path,
            'width' => $options['width'] ?? $alertImage->width ?? 720,
            'height' => $options['height'] ?? $alertImage->height ?? 960,
            'fit' => $options['fit'] ?? $alertImage->fit ?? 'CONTAIN',
        ]);

        return $alertImage->fresh();
    }

    public function delete(AlertImage $alertImage): bool
    {
        if ($alertImage->path && Storage::disk('public')->exists($alertImage->path)) {
            Storage::disk('public')->delete($alertImage->path);
        }

        return (bool) $alertImage->delete();
    }
}
