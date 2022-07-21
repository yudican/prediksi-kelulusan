<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataProdi extends Model
{
    //use Uuid;
    use HasFactory;
    protected $table = 'data_prodi';
    //public $incrementing = false;

    protected $fillable = ['kode_prodi', 'nama_prodi', 'user_id'];

    protected $dates = [];

    /**
     * Get all of the dataMahasiswa for the DataProdi
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dataMahasiswa()
    {
        return $this->hasMany(DataMahasiswa::class);
    }

    /**
     * Get the user that owns the DataProdi
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
