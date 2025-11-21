<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrReader extends Model
{
    protected $table = 'qr_readers';
    public $timestamps = false;
    protected $fillable = [
        'serial_number', 
        'sense', 
        'id_parking'
    ];

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'id_parking');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id_qr_reader');
    }
}
