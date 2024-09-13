<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RekeningAdmin extends Model
{
    use HasFactory;
    protected $table = 'rekening_admins';
    protected $fillable = ['bank_id', 'atas_nama', 'nomor_rekening'];
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

    public function bank() {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function transaksiTransfer() {
        return $this->hasMany(TransaksiTransfer::class, 'bank_perantara_id');
    }
}
