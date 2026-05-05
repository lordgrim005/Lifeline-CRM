<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CameraModel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brand',
        'name',
        'description',
    ];

    public function packages()
    {
        return $this->hasMany(CameraPackage::class);
    }

    public function cameras()
    {
        return $this->hasMany(Camera::class);
    }
}
