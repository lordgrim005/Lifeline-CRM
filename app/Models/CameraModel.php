<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CameraModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['model_name'];

    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class, 'model_id');
    }
}
