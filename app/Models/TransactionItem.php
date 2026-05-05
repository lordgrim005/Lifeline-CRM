<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'camera_id',
        'camera_package_id',
        'price_per_day',
        'subtotal',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }

    public function cameraPackage()
    {
        return $this->belongsTo(CameraPackage::class);
    }
}
