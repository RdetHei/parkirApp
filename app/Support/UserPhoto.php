<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserPhoto
{
    /**
     * Upload a new avatar to Cloudinary and return attributes to merge onto the user model.
     * Removes any previous Cloudinary asset or local storage file for this user.
     *
     * @return array{photo: null, photo_cloudinary_path: string}
     */
    public static function replaceWithUpload(UploadedFile $file, User $user): array
    {
        self::purgeFromDisk($user);

        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg'));
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'jpg';
        $path = 'avatars/user-'.$user->id.'-'.Str::uuid()->toString().'.'.$ext;

        // Cloudinary's upload API treats non-URL strings as *file paths* and calls fopen() on them.
        // Raw image bytes can contain null bytes → ValueError. Upload via stream from temp path instead.
        $realPath = $file->getRealPath();
        if ($realPath === false || ! is_readable($realPath)) {
            throw new \RuntimeException('File upload tidak valid atau tidak dapat dibaca.');
        }

        $stream = fopen($realPath, 'rb');
        try {
            Storage::disk('cloudinary')->put($path, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return [
            'photo' => null,
            'photo_cloudinary_path' => $path,
        ];
    }

    public static function purgeFromDisk(User $user): void
    {
        if (filled($user->photo_cloudinary_path)) {
            try {
                Storage::disk('cloudinary')->delete($user->photo_cloudinary_path);
            } catch (\Throwable) {
            }
        }

        if (filled($user->photo) && ! self::isRemoteUrl($user->photo)) {
            try {
                Storage::disk('public')->delete($user->photo);
            } catch (\Throwable) {
            }
        }
    }

    public static function isRemoteUrl(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}
