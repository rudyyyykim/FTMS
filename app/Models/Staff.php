<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $primaryKey = 'staffID';
    public $incrementing = true;

    protected $fillable = [
        'staffName',
        'staffPosition',
        'staffPhone',
        'staffEmail'
    ];

    public function fileRequests()
    {
        return $this->hasMany(FileRequest::class, 'staffID', 'staffID');
    }
}