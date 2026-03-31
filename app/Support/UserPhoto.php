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

        $upload = cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder' => 'neston/profile',
            'public_id' => 'user-' . $user->id . '-' . Str::random(8)
        ]);

        return [
            'photo' => null,
            'photo_cloudinary_path' => $upload['secure_url'],
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
