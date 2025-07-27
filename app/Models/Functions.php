<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Functions extends Model
{
    use HasFactory;

    protected $table = 'functions';
    protected $primaryKey = 'functionCode';
    public $incrementing = false;
    protected $keyType = 'int';  // Changed from string to int

    protected $fillable = [
        'functionCode',
        'functionName',
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class, 'functionCode', 'functionCode');
    }
}