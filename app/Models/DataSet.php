<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSet extends Model
{
    //use Uuid;
    use HasFactory;

    //public $incrementing = false;

    protected $fillable = ['angkatan', 'tgl_masuk', 'tgl_yudisium', 'nama_prodi', 'lama_kuliah', 'status', 'ipk', 'sks', 'target'];

    protected $dates = [];
}
