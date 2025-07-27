<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileRequest extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_TIADA = 'Tiada';
    const STATUS_DALAM_PROSES = 'Dalam proses';
    const STATUS_BERJAYA = 'Berjaya';
    const STATUS_DIBATALKAN = 'Dibatalkan';

    protected $table = 'fileRequest';
    protected $primaryKey = 'requestID';
    public $incrementing = true;

    protected $fillable = [
        'fileID',
        'staffID',
        'requestDate',
        'reserveStatus',
        'reserveDate',
        'isReservation',
    ];

    protected $attributes = [
        'reserveStatus' => self::STATUS_TIADA,
    ];

    protected $casts = [
        'requestDate' => 'date',
        'reserveDate' => 'date',
        'isReservation' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffID', 'staffID');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'fileID', 'fileID');
    }

    public function fileReturn()
    {
        return $this->hasOne(FileReturn::class, 'requestID', 'requestID');
    }

    // Status transition methods
    public function setDalamProses()
    {
        $this->reserveStatus = self::STATUS_DALAM_PROSES;
        return $this->save();
    }

    public function setBerjaya()
    {
        $this->reserveStatus = self::STATUS_BERJAYA;
        return $this->save();
    }

    public function setDibatalkan()
    {
        $this->reserveStatus = self::STATUS_DIBATALKAN;
        return $this->save();
    }

    // Status check methods
    public function isTiada(): bool
    {
        return $this->reserveStatus === self::STATUS_TIADA;
    }

    public function isDalamProses(): bool
    {
        return $this->reserveStatus === self::STATUS_DALAM_PROSES;
    }

    public function isBerjaya(): bool
    {
        return $this->reserveStatus === self::STATUS_BERJAYA;
    }

    public function isDibatalkan(): bool
    {
        return $this->reserveStatus === self::STATUS_DIBATALKAN;
    }

    // Get all possible statuses (for dropdowns, etc.)
    public static function getAllStatuses(): array
    {
        return [
            self::STATUS_TIADA,
            self::STATUS_DALAM_PROSES,
            self::STATUS_BERJAYA,
            self::STATUS_DIBATALKAN,
        ];
    }
}