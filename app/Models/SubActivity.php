<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubActivity extends Model
{
    use HasFactory;

    protected $table = 'sub_activities';
    // For composite primary keys, we don't define primaryKey
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'subActivityCode',
        'activityCode',
        'functionCode',
        'subActivityName',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activityCode', 'activityCode');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'subActivityCode', 'subActivityCode');
    }
}