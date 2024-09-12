<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RekeningPengguna extends Model
{
    use HasFactory;
    protected $table = 'rekening_penggunas';
    protected $fillable = ['user_id', 'bank_id', 'atas_nama', 'nomor_rekening'];
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

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bank() {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function transaksiSebagaiPengirim() {
        return $this->hasMany(TransaksiTransfer::class, 'pengirim_id');
    }

    public function transaksiSebagaiPenerima() {
        return $this->hasMany(TransaksiTransfer::class, 'penerima_id');
    }
}
