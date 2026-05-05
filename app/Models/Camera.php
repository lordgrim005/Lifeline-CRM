<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Camera extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'camera_model_id',
        'serial_number',
        'status',
    ];

    public function cameraModel()
    {
        return $this->belongsTo(CameraModel::class);
    }
}
