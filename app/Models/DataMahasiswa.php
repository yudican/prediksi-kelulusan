<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataMahasiswa extends Model
{
    //use Uuid;
    use HasFactory;
    protected $table = 'data_mahasiswa';

    //public $incrementing = false;

    protected $fillable = ['nama', 'nim', 'angkatan', 'tgl_masuk', 'tgl_yudisium', 'data_prodi_id', 'lama_kuliah', 'jenis_kelamin', 'agama', 'status', 'ipk', 'sks', 'penghasilan'];

    protected $dates = [];

    public function dataProdi()
    {
        return $this->belongsTo(DataProdi::class);
    }
}
