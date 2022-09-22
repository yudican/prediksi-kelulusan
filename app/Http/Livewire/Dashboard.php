<?php

namespace App\Http\Livewire;

use App\Models\DataMahasiswa;
use App\Models\DataProdi;
use App\Models\DataSet;
use Livewire\Component;
use App\Http\Controllers\NaiveBayes;
use App\Http\Controllers\NaiveBayesClasifier;

class Dashboard extends Component
{
    public $dataChart;
    public function mount()
    {
        $samples = [];
        $datas = [];
        $members = [];
        $labels = ['Angkatan', 'Tanggal Masuk', 'Tanggal Yudisium', 'Lama Kuliah', 'Prodi', 'Status', 'IPK', 'SKS'];
        $newLable = [];
        $newSample = [];
        $user = auth()->user();
        $data_mahasiswa = DataMahasiswa::all();
        $prodi_id = $user->dataProdi ? $user->dataProdi->id : null;
        if ($prodi_id) {
            $data_mahasiswa = DataMahasiswa::where('data_prodi_id', $prodi_id)->get();
        }
        $data_set = DataSet::all();

        $role = $user->role->role_type;
        if ($role == 'member') {
            $data_mahasiswa = DataMahasiswa::whereHas('dataProdi', function ($query) use ($user) {
                return $query->where('user_id', $user->id);
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
                intval($set->lama_kuliah),
                $set->nama_prodi,
                $set->status,
                floatval($set->ipk),
                $set->sks,
            ];
        }
        // dd($newSample);
        $classifier = new NaiveBayes($samples, $labels);
        $clasification = new NaiveBayesClasifier();
        $clasification->train($newSample, $newLable);

        $total = [];
        $chartLabels = [];
        foreach ($data_mahasiswa as $key => $value) {
            $result = $clasification->predict([
                $value->angkatan,
                $value->tgl_masuk,
                $value->tgl_yudisium,
                $value->lama_kuliah,
                $value->dataProdi->nama_prodi,
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
                'result' => $result
            ];
            $datas[$value->nim] = [$result];
            $chartLabels[$value->dataProdi->id][] = [
                'prodi' => $value->dataProdi->nama_prodi,
                'result' => $result
            ];
        }

        $labelValue = [];
        $angkatans = [];

        $total = [];
        $charts = [];
        $newCharts = [];
        foreach ($data_mahasiswa as $key => $value) {
            $keyData = $value->dataProdi->nama_prodi . '-' . $value->angkatan;
            $labelValue[$keyData] = $value->dataProdi->nama_prodi . '_' . $keyData;
            $angkatans[$keyData] = $value->angkatan;
            $charts[$value->dataProdi->nama_prodi] = $angkatans;

            if (isset($total[$keyData])) {
                $total[$keyData] = $total[$keyData] + 1;
            } else {
                $total[$keyData] = 1;
            }
        }

        $angkatan = [];
        foreach ($charts as $key => $chart) {
            foreach ($chart as $index => $value) {
                $angkatan[$value] = $value;
                if (isset($newCharts[$key])) {
                    $newCharts[$key][$value] = $total[$index];
                } else {
                    $newCharts[$key] = [$value => $total[$index]];
                }
            }
        }

        $dataChart = [
            'barLabels' => array_keys($newCharts),
            'barValues' => array_values($newCharts),
            'barAngkatan' => array_values($angkatan),
        ];
        $this->dataChart = $dataChart;
    }
    public function render()
    {
        return view('livewire.dashboard', [
            'prodis' => DataProdi::all(),
        ]);
    }
}
