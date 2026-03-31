<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Support\UserPhoto;

class User extends Authenticatable
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_user';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'saldo',
        'rfid_uid',
        'nfc_uid',
        'balance',
        'photo',
        'photo_cloudinary_path',
    ];

    public function saldoHistories()
    {
        return $this->hasMany(SaldoHistory::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * URL for profile photo (Cloudinary, legacy local path, or full URL in photo).
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (filled($this->photo_cloudinary_path)) {
            if (str_starts_with($this->photo_cloudinary_path, 'http')) {
                return $this->photo_cloudinary_path;
            }
            try {
                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
                $disk = Storage::disk('cloudinary');
                return $disk->url($this->photo_cloudinary_path);
            } catch (\Throwable) {
                return null;
            }
        }

        if (filled($this->photo)) {
            if (UserPhoto::isRemoteUrl($this->photo)) {
                return $this->photo;
            }

            return asset('storage/'.$this->photo);
        }

        return null;
    }
}
