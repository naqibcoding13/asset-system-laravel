<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RequestItem extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pending';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';

    protected $fillable = [
        'request_id',
        'jenis_aset',
        'perincian_aset',
        'kuantiti',
        'harga_seunit',
        'jumlah',
        'justifikasi',
        'quotation',
        'status',
    ];

    public function request()
    {
        return $this->belongsTo(AssetRequest::class, 'request_id');
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Dalam Proses',
            self::STATUS_APPROVED => 'Diluluskan',
            self::STATUS_REJECTED => 'Ditolak',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? (string) $this->status;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'text-bg-success',
            self::STATUS_REJECTED => 'text-bg-danger',
            default => 'text-bg-warning',
        };
    }

    public function categoryLabel(): string
    {
        $label = data_get(config('asset_system.asset_categories'), "{$this->jenis_aset}.label");

        if ($label) {
            return trim($this->jenis_aset . ' - ' . $label);
        }

        return (string) $this->jenis_aset;
    }

    public function displayName(): string
    {
        $detail = trim((string) $this->perincian_aset);

        if ($detail === '') {
            return $this->categoryLabel();
        }

        return $this->categoryLabel() . ' - ' . $detail;
    }

    public function quotationPath(): ?string
    {
        $path = trim((string) $this->quotation);

        if ($path === '') {
            return null;
        }

        $normalizedPath = str_replace('\\', '/', $path);

        foreach ([
            $normalizedPath,
            preg_replace('#^(storage|public)/#', '', $normalizedPath),
            'quotations/' . basename($normalizedPath),
        ] as $candidate) {
            if ($candidate && Storage::disk('public')->exists($candidate)) {
                return $candidate;
            }
        }

        return preg_replace('#^(storage|public)/#', '', $normalizedPath);
    }

    public function hasQuotationFile(): bool
    {
        $path = $this->quotationPath();

        return $path !== null && Storage::disk('public')->exists($path);
    }
}
