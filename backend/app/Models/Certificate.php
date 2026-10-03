<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'certificate_code',
        'tenant_id',
        'cohort_id',
        'course_id',
        'user_id',
        'issued_at',
        'pdf_path',
        'qr_code_path',
        'grade_percentage',
        'metadata',
        'is_revoked',
        'revocation_reason',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'grade_percentage' => 'decimal:2',
            'metadata' => 'array',
            'is_revoked' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCode(): string
    {
        $year = date('Y');
        $random = strtoupper(bin2hex(random_bytes(4)));
        return "LX-{$year}-{$random}";
    }
}
