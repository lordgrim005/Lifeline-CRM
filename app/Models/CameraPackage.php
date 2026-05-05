<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CameraPackage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'camera_model_id',
        'package_name',
        'includes',
        'daily_price',
    ];

    public function cameraModel()
    {
        return $this->belongsTo(CameraModel::class);
    }
}
