<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_STUDENT = 'student';
    public const ROLE_WORKER = 'worker';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'dorm_id',
        'is_active',
    ];

    /**
     * Defaults for new accounts (mirrors the database default).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
    ];

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
            'is_active' => 'boolean',
            'avatar_updated_at' => 'datetime',
        ];
    }

    // ----- Profile photo --------------------------------------------------

    public function avatar(): HasOne
    {
        return $this->hasOne(UserAvatar::class);
    }

    public function hasAvatar(): bool
    {
        return $this->avatar_updated_at !== null;
    }

    /** URL of the photo, or null when the user has none. Changes whenever the photo changes. */
    public function avatarUrl(): ?string
    {
        return $this->hasAvatar()
            ? route('avatars.show', ['user' => $this->id, 'v' => $this->avatar_updated_at->timestamp])
            : null;
    }

    public function setAvatar(UploadedFile $file): void
    {
        $this->avatar()->updateOrCreate([], [
            'mime' => $file->getMimeType(),
            'data' => base64_encode($file->get()),
        ]);
        $this->forceFill(['avatar_updated_at' => now()])->save();
    }

    public function removeAvatar(): void
    {
        $this->avatar()->delete();
        $this->forceFill(['avatar_updated_at' => null])->save();
    }

    // ----- Role helpers -------------------------------------------------

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function isWorker(): bool
    {
        return $this->role === self::ROLE_WORKER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // ----- Admin responsibilities ------------------------------------------

    /** The Head of Residence Life creates admins and assigns their responsibilities. */
    public function isHeadAdmin(): bool
    {
        return $this->isAdmin() && $this->adminPermissions->contains('permission', AdminPermission::ADMINS);
    }

    /** Whether this admin may use an admin area. The Head of Residence Life may use them all. */
    public function hasAdminPermission(string $permission): bool
    {
        if (! $this->isAdmin() || ! $this->is_active) {
            return false;
        }

        return $this->isHeadAdmin() || $this->adminPermissions->contains('permission', $permission);
    }

    /** Job title shown in the interface for admin accounts. */
    public function adminTitle(): string
    {
        return $this->isHeadAdmin() ? 'Head of Residence Life' : 'Administrator';
    }

    // ----- Relationships ------------------------------------------------

    public function dorm(): BelongsTo
    {
        return $this->belongsTo(Dorm::class);
    }

    public function adminPermissions(): HasMany
    {
        return $this->hasMany(AdminPermission::class);
    }

    public function workerProfile(): HasOne
    {
        return $this->hasOne(WorkerProfile::class);
    }

    /** Orders this user placed as a student. */
    public function ordersAsStudent(): HasMany
    {
        return $this->hasMany(Order::class, 'student_id');
    }

    /** Orders assigned to this user as a worker. */
    public function ordersAsWorker(): HasMany
    {
        return $this->hasMany(Order::class, 'worker_id');
    }

    /** Ratings this user has received. */
    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'ratee_id');
    }

    /** Ratings this user has given. */
    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    public function complaintsFiled(): HasMany
    {
        return $this->hasMany(Complaint::class, 'complainant_id');
    }
}
