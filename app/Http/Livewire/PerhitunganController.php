<?php

namespace App\Http\Livewire;

use App\Http\Controllers\NaiveBayes;
use App\Models\DataMahasiswa;
use App\Models\DataProdi;
use App\Models\DataSet;
use Livewire\Component;
use Phpml\Classification\NaiveBayes as ClassificationNaiveBayes;

class PerhitunganController extends Component
{
    public $prodi_id;
    public $angkatan;
    public function mount()
    {
        if (auth()->user()->role->role_type == 'member') {
            $this->prodi_id = auth()->user()->dataProdi->id;
        }
    }


    public function render()
    {
        $samples = [];
        $members = [];
        $labels = ['Angkatan', 'Tanggal Masuk', 'Tanggal Yudisium', 'Lama Kuliah', 'Prodi', 'Status', 'IPK', 'SKS'];
        $newLable = [];
        $newSample = [];
        $data_mahasiswa = DataMahasiswa::all();
        if ($this->prodi_id || $this->angkatan) {
            $data_mahasiswa = DataMahasiswa::where('data_prodi_id', $this->prodi_id)->orWhere('angkatan', $this->angkatan)->get();
        }
        $data_set = DataSet::all();

        $role = auth()->user()->role->role_type;
        if ($role == 'member') {
            $data_mahasiswa = DataMahasiswa::whereHas('dataProdi', function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })->get();
        }

        foreach ($data_set as $key => $set) {
            $newLable[] = $set->target;
            $samples[] = [
                $set->angkatan,
                $set->tgl_masuk,
                $set->tgl_yudisium,
                $set->lama_kuliah,
                $set->nama_prodi,
                $set->status,
                $set->ipk,
                $set->sks,
                $set->target,
            ];

            $newSample[] = [
                $set->angkatan,
                $set->tgl_masuk,
                $set->tgl_yudisium,
                $set->lama_kuliah,
                $set->nama_prodi,
                $set->status,
                $set->ipk,
                $set->sks,
            ];
        }

        $classifier = new NaiveBayes($samples, $labels);
        $clasification = new ClassificationNaiveBayes();
        $clasification->train($newSample, $newLable);
        foreach ($data_mahasiswa as $key => $value) {
            $members[] = [
                'user' => [
                    // $value->nim,
                    // $value->nama,
                    $value->angkatan,
                    $value->tgl_masuk,
                    $value->tgl_yudisium,
                    $value->lama_kuliah,
                    $value->dataProdi->nama_prodi,
                    $value->status,
                    $value->ipk,
                    $value->sks,
                ],
                'perhitungan' => $classifier->run()->predict([
                    $value->angkatan,
                    $value->tgl_masuk,
                    $value->tgl_yudisium,
                    $value->lama_kuliah,
                    $value->dataProdi->nama_prodi,
                    $value->status,
                    $value->ipk,
                    $value->sks,
                ]),
                'result' => $clasification->predict([
                    $value->angkatan,
                    $value->tgl_masuk,
                    $value->tgl_yudisium,
                    $value->lama_kuliah,
                    $value->dataProdi->nama_prodi,
                    $value->status,
                    $value->ipk,
                    $value->sks,
                ])
            ];
        }

        return view('livewire.perhitungan', [
            'data' => $members,
            'labels' => $labels,
            'data_prodi' => DataProdi::all(),
            'role' => $role
        ]);
    }
}
