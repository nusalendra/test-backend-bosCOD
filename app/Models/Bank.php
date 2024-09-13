<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bank extends Model
{
    use HasFactory;
    protected $table = 'banks';
    protected $fillable = ['nama', 'kode'];
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

    public function rekeningAdmin() {
        return $this->hasOne(RekeningAdmin::class, 'bank_id');
    }

    public function rekeningPengguna() {
        return $this->hasMany(RekeningPengguna::class, 'bank_id');
    }
}
