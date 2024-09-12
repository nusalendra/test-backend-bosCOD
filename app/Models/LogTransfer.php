<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LogTransfer extends Model
{
    use HasFactory;
    protected $table = 'log_transfers';
    protected $fillable = ['transaksi_id', 'status'];
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function transaksiTransfer() {
        return $this->belongsTo(TransaksiTransfer::class, 'transaksi_id');
    }
}
