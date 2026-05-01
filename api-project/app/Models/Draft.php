<?php

namespace App\Models;

use App\Enums\DraftStatus;
use App\Enums\Platform;
use Database\Factories\DraftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'application_id',
    'revision_number',
    'platform',
    'file_path',
    'file_url',
    'caption',
    'status',
    'submitted_at',
    'reviewed_at',
    'reviewed_by',
    'review_note',
])]
class Draft extends Model
{
    /** @use HasFactory<DraftFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => DraftStatus::class,
            'platform' => Platform::class,
            'revision_number' => 'integer',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isSubmitted(): bool
    {
        return $this->status === DraftStatus::Submitted;
    }

    public function isApproved(): bool
    {
        return $this->status === DraftStatus::Approved;
    }
}
