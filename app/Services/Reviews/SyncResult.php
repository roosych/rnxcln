<?php

namespace App\Services\Reviews;

final class SyncResult
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    public int $errors = 0;

    /** @var list<string> */
    public array $errorMessages = [];

    public bool $unsupported = false;

    public function created(): static
    {
        $this->created++;

        return $this;
    }

    public function updated(): static
    {
        $this->updated++;

        return $this;
    }

    public function skipped(): static
    {
        $this->skipped++;

        return $this;
    }

    public function failed(string $message): static
    {
        $this->errors++;
        $this->errorMessages[] = $message;

        return $this;
    }

    public function markUnsupported(string $message): static
    {
        $this->unsupported = true;
        $this->errorMessages[] = $message;

        return $this;
    }

    public function hasErrors(): bool
    {
        return $this->errors > 0 || $this->unsupported;
    }

    public function summary(): string
    {
        return sprintf(
            'New: %d, Updated: %d, Skipped: %d, Errors: %d',
            $this->created,
            $this->updated,
            $this->skipped,
            $this->errors
        );
    }
}
