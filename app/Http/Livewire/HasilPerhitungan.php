<?php

namespace App\Http\Livewire;

use App\Exports\HasilperhitunganExport;
use App\Http\Controllers\NaiveBayes;
use App\Models\DataMahasiswa;
use App\Models\DataProdi;
use App\Models\DataSet;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Phpml\Classification\NaiveBayes as ClassificationNaiveBayes;

class HasilPerhitungan extends Component
{
    public $prodi_id;
    public $datas = [];
    public function render()
    {
        $samples = [];
        $datas = [];
        $members = [];
        $labels = ['Angkatan', 'Jenjang', 'Prodi', 'Type Kelas', 'Status', 'IPK', 'SKS'];
        $newLable = [];
        $newSample = [];
        $data_mahasiswa = DataMahasiswa::all();
        $this->emit('changeData', $this->prodi_id);
        if ($this->prodi_id) {
            $data_mahasiswa = DataMahasiswa::where('data_prodi_id', $this->prodi_id)->get();
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
                $set->jenjang,
                $set->nama_prodi,
                $set->type_kelas,
                $set->status,
                $set->ipk,
                $set->sks,
                $set->target,
            ];

            $newSample[] = [
                $set->angkatan,
                $set->jenjang,
                $set->nama_prodi,
                $set->type_kelas,
                $set->status,
                $set->ipk,
                $set->sks,
            ];
        }

        $classifier = new NaiveBayes($samples, $labels);
        $clasification = new ClassificationNaiveBayes();
        $clasification->train($newSample, $newLable);
        $total = [];
        foreach ($data_mahasiswa as $key => $value) {
            $result = $clasification->predict([
                $value->angkatan,
                $value->jenjang,
                $value->dataProdi->nama_prodi,
                $value->type_kelas,
                $value->status,
                $value->ipk,
                $value->sks,
            ]);
            $members[] = [
                'user_detail' => [
                    $value->nim,
                    $value->nama,
                ],
                'user' => [
                    $value->angkatan,
                    $value->jenjang,
                    $value->dataProdi->nama_prodi,
                    $value->type_kelas,
                    $value->status,
                    $value->ipk,
                    $value->sks,
                ],
                'perhitungan' => $classifier->run()->predict([
                    $value->angkatan,
                    $value->jenjang,
                    $value->dataProdi->nama_prodi,
                    $value->type_kelas,
                    $value->status,
                    $value->ipk,
                    $value->sks,
                ]),
                'result' => $result
            ];

            $datas[$value->nim] = [$result];
        }
        $this->datas = $datas;
        return view('livewire.hasil-perhitungan', [
            'data' => $members,
            'labels' => $labels,
            'data_prodi' => DataProdi::all(),
            'role' => $role
        ]);
    }

    public function export()
    {
        return Excel::download(new HasilperhitunganExport($this->datas), 'hasil-perhitungan.xlsx');
    }
}
