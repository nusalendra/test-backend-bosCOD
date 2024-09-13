<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransaksiTransfer extends Model
{
    use HasFactory;
    protected $table = 'transaksi_transfers';
    protected $fillable = ['pengirim_id', 'penerima_id', 'bank_perantara_id', 'biaya_admin', 'kode_unik', 'nilai_transfer', 'total_transfer', 'berlaku_hingga'];
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });

        static::creating(function ($transaksiTransfer) {
            $transaksiTransfer->berlaku_hingga = Carbon::now()->addDays(3);
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

    public function pengirimTransaksiTransfer() {
        return $this->belongsTo(RekeningPengguna::class, 'pengirim_id');
    }

    public function penerimaTransaksiTransfer() {
        return $this->belongsTo(RekeningPengguna::class, 'penerima_id');
    }

    public function rekeningAdmin() {
        return $this->belongsTo(Bank::class, 'bank_perantara_id');
    }

    public function logTransfer()
    {
        return $this->hasOne(LogTransfer::class, 'transaksi_id');
    }
}
