<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';
    protected $primaryKey = 'fileID';
    public $incrementing = true;

    protected $fillable = [
        'fileCode',
        'activityCode',
        'subActivityCode',
        'functionCode',
        'fileName',
        'fileDescription',
        'fileLocation',
        'fileLevel',
        'fileStatus',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activityCode', 'activityCode');
    }

    public function subActivity()
    {
        return $this->belongsTo(SubActivity::class, 'subActivityCode', 'subActivityCode');
    }

    public function functions()
    {
        return $this->belongsTo(Functions::class, 'functionCode', 'functionCode');
    }

    public function fileRequests()
    {
        return $this->hasMany(FileRequest::class, 'fileID', 'fileID');
    }

    public function getBorrowStatusAttribute()
    {
        // Check if there's an active file request with return status "Belum Dipulangkan"
        $activeRequest = $this->fileRequests()
            ->whereHas('fileReturn', function($query) {
                $query->where('returnStatus', 'Belum Dipulangkan');
            })
            ->exists();

        // If there's an active request with "Belum Dipulangkan" status, file is borrowed
        if ($activeRequest) {
            return 'Dipinjam';
        }

        // Check if there's a request without any return record (excluding cancelled reservations)
        $requestWithoutReturn = $this->fileRequests()
            ->whereDoesntHave('fileReturn')
            ->where(function($query) {
                $query->where('isReservation', false)  // Regular requests without returns
                      ->orWhere(function($subQuery) {
                          // Or reservations that are not cancelled
                          $subQuery->where('isReservation', true)
                                   ->where('reserveStatus', '!=', 'Dibatalkan');
                      });
            })
            ->exists();

        if ($requestWithoutReturn) {
            return 'Dipinjam';
        }

        // Check for active reservations that are still in process
        $activeReservationInProcess = $this->fileRequests()
            ->where('isReservation', true)
            ->where('reserveStatus', 'Dalam Proses')
            ->exists();

        if ($activeReservationInProcess) {
            return 'Dipinjam';
        }

        // Check for successful reservations that haven't been returned yet
        $activeSuccessfulReservation = $this->fileRequests()
            ->where('isReservation', true)
            ->where('reserveStatus', 'Berjaya')
            ->whereHas('fileReturn', function($query) {
                $query->where('returnStatus', 'Belum Dipulangkan');
            })
            ->exists();

        if ($activeSuccessfulReservation) {
            return 'Dipinjam';
        }

        return 'Tersedia';
    }
}