<?php

namespace API\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ApiTokenService
{
    private const TOKEN_PREFIX = 'api_auth_token:';
    private const TOKEN_TTL_MINUTES = 60 * 24 * 7; // 7 días

    public function issueTokenForUser(User $user): array
    {
        $plainToken = Str::random(80);
        $tokenHash = hash('sha256', $plainToken);

        Cache::put(
            $this->cacheKey($tokenHash),
            [
                'user_id' => $user->id,
                'issued_at' => now()->toIso8601String(),
            ],
            now()->addMinutes(self::TOKEN_TTL_MINUTES)
        );

        return [
            'access_token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_in' => self::TOKEN_TTL_MINUTES * 60,
        ];
    }

    public function getUserFromToken(string $plainToken): ?User
    {
        $tokenHash = hash('sha256', $plainToken);
        $payload = Cache::get($this->cacheKey($tokenHash));

        if (!is_array($payload) || !isset($payload['user_id'])) {
            return null;
        }

        return User::find($payload['user_id']);
    }

    public function revokeToken(string $plainToken): void
    {
        $tokenHash = hash('sha256', $plainToken);
        Cache::forget($this->cacheKey($tokenHash));
    }

    private function cacheKey(string $tokenHash): string
    {
        return self::TOKEN_PREFIX.$tokenHash;
    }
}
