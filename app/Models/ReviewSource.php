<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewSource extends Model
{
    use HasFactory;

    public const PROVIDER_GOOGLE = 'google';

    public const PROVIDER_YELP = 'yelp';

    public const PROVIDER_FACEBOOK = 'facebook';

    public const PROVIDER_MANUAL = 'manual';

    public const PROVIDER_CUSTOM = 'custom';

    public const TYPE_OAUTH = 'oauth';

    public const TYPE_API_KEY = 'api_key';

    public const TYPE_MANUAL = 'manual';

    public const STATUS_IDLE = 'idle';

    public const STATUS_SYNCING = 'syncing';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_ERROR = 'error';

    public const STATUS_UNSUPPORTED = 'unsupported';

    protected $fillable = [
        'name',
        'provider',
        'type',
        'enabled',
        'connected',
        'config',
        'credentials',
        'last_synced_at',
        'sync_status',
        'sync_error',
    ];

    protected $hidden = [
        'credentials',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'connected' => 'boolean',
            'config' => 'array',
            // Encrypted at rest — API keys and OAuth tokens never sit in the
            // database (or a backup dump) in plaintext.
            'credentials' => AsEncryptedArrayObject::class,
            'last_synced_at' => 'datetime',
        ];
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function scopeConnected(Builder $query): Builder
    {
        return $query->where('connected', true);
    }

    public function isManual(): bool
    {
        return $this->provider === self::PROVIDER_MANUAL;
    }

    /** @return array<string, mixed> */
    public function credentialsArray(): array
    {
        return $this->credentials?->getArrayCopy() ?? [];
    }

    public function credential(string $key, mixed $default = null): mixed
    {
        return $this->credentialsArray()[$key] ?? $default;
    }

    public function configValue(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /** Last 4 chars of a stored secret, safe to render in the admin UI. */
    public function maskedCredential(string $key): ?string
    {
        $value = $this->credential($key);

        if (! is_string($value) || $value === '') {
            return null;
        }

        return str_repeat('•', 8).substr($value, -4);
    }
}
