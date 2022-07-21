<?php

namespace App\Imports;

use App\Models\DataMahasiswa;
use App\Models\DataProdi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

class DataMahasiswaImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        $prodi = DataProdi::where('nama_prodi', 'like', "%{$row[5]}%")->first();
        return new DataMahasiswa([
            'nama'     => $row[1],
            'nim'    => $row[2],
            'angkatan'    => $row[3],
            'jenjang'    => $row[4],
            'data_prodi_id'    => $prodi ? $prodi->id : 1,
            'type_kelas'    => ucfirst(strtolower($row[6])),
            'jenis_kelamin'    => $row[7],
            'agama'    => ucfirst(strtolower($row[8])),
            'status'    => $row[9],
            'ipk'    => $row[11] ?? 0,
            'sks'    => $row[10] ?? 0,
            'penghasilan'    => $row[12] ?? 0,
        ]);
    }
}
