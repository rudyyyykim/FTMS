<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    // For composite primary keys, we don't define primaryKey
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'activityCode',
        'functionCode',
        'activityName',
    ];

    public function function()
    {
        return $this->belongsTo(Functions::class, 'functionCode', 'functionCode');
    }

    public function subActivities()
    {
        return $this->hasMany(SubActivity::class, 'activityCode', 'activityCode');
    }
}