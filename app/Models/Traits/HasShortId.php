<?php

namespace App\Models\Traits;

use Sqids\Sqids;

trait HasShortId
{
    protected function sqids(): Sqids
    {
        return new Sqids(
            alphabet: 'fx3k9p2m7q8w4z1n5v6bcd0g',
            minLength: 8
        );
    }

    public function getRouteKey(): string
    {
        return $this->sqids()->encode([$this->getKey()]);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $decoded = $this->sqids()->decode($value);

        if (empty($decoded) || $this->sqids()->encode([$decoded[0]]) !== $value) {
            abort(404);
        }

        return $this->where($field ?? $this->getRouteKeyName(), $decoded[0])->firstOrFail();
    }

    public function getShortIdAttribute(): string
    {
        return $this->getRouteKey();
    }
}
