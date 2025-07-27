<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileReturn extends Model
{
    use HasFactory;

    protected $table = 'filereturn';
    protected $primaryKey = 'returnID';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'requestID',
        'userID',
        'returnDate',
        'returnStatus',
        'returnTiming', // Using the actual column name in database
        'updatedReturnDate' // Using the actual column name in database
    ];

    protected $casts = [
        'returnDate' => 'date',
        'updatedReturnDate' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function fileRequest()
    {
        return $this->belongsTo(FileRequest::class, 'requestID', 'requestID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }
}