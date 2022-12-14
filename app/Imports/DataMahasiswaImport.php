<?php

namespace App\Imports;

use App\Models\DataMahasiswa;
use App\Models\DataProdi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DataMahasiswaImport implements ToCollection, WithStartRow
{
    // /**
    //  * @param array $row
    //  *
    //  * @return User|null
    //  */
    // public function model(array $row)
    // {
    //     $prodi = DataProdi::where('nama_prodi', 'like', "%{$row[5]}%")->first();
    //     return new DataMahasiswa([
    //         'nama'     => $row[1],
    //         'nim'    => $row[2],
    //         'angkatan'    => $row[3],
    //         'data_prodi_id'    => $prodi ? $prodi->id : 1,
    //         'jenjang'    => $row[4],
    //         'type_kelas'    => ucfirst(strtolower($row[6])),
    //         'jenis_kelamin'    => $row[7],
    //         'agama'    => ucfirst(strtolower($row[8])),
    //         'status'    => $row[9],
    //         'ipk'    => $row[11] ?? 0,
    //         'sks'    => $row[10] ?? 0,
    //         'penghasilan'    => $row[12] ?? 0,
    //     ]);
    // }

    // start row = 9
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 9;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            $prodi = DataProdi::where('nama_prodi', 'like', "%{$row[5]}%")->first();
            try {
                DB::beginTransaction();
                DataMahasiswa::create([
                    'nama'     => $row[1],
                    'nim'    => $row[2],
                    'angkatan'    => $row[3],
                    'data_prodi_id'    => $prodi ? $prodi->id : 1,
                    'tgl_masuk'    => $row[3] . '-01-01',
                    'tgl_yudisium'    => null,
                    'lama_kuliah'    => 0,
                    'jenis_kelamin'    => $row[7],
                    'agama'    => ucfirst(strtolower($row[8])),
                    'status'    => $row[9],
                    'ipk'    => $row[11] ?? 0,
                    'sks'    => $row[10] ?? 0,
                    'penghasilan' => 0
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
            }
        }
    }
}
