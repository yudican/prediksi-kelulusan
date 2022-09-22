<?php

namespace App\Http\Livewire;

use App\Exports\HasilperhitunganExport;
use App\Http\Controllers\NaiveBayes;
use App\Http\Controllers\NaiveBayesClasifier;
use App\Models\DataMahasiswa;
use App\Models\DataProdi;
use App\Models\DataSet;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class HasilPerhitungan extends Component
{
    public $prodi_id;
    public $datas = [];
    public function render()
    {
        $data = $this->changeData($this->prodi_id);
        $this->datas = $data['data'];
        return view('livewire.hasil-perhitungan', [
            'data' => $data['members'],
            'labels' => $data['labels'],
            'data_prodi' => DataProdi::all(),
            'role' => $data['role'],
            'dataChart' => $data['dataChart'],
        ]);
    }

    public function export()
    {
        return Excel::download(new HasilperhitunganExport($this->datas), 'hasil-perhitungan.xlsx');
    }

    public function changeData($prodi_id = null)
    {
        $samples = [];
        $datas = [];
        $members = [];
        $labels = ['Angkatan', 'Tanggal Masuk', 'Tanggal Yudisium', 'Lama Kuliah', 'Prodi', 'Status', 'IPK', 'SKS'];
        $newLable = [];
        $newSample = [];
        $data_mahasiswa = DataMahasiswa::all();
        $this->emit('changeData', $prodi_id);
        if ($prodi_id) {
            $data_mahasiswa = DataMahasiswa::where('data_prodi_id', $prodi_id)->get();
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


        $final_data = [];
        $final_label = [];
        $final_data_fix = [];
        foreach ($chartLabels as $key => $label) {
            $final_label[$key] = $label[$key]['prodi'];
            foreach ($label as $index => $value) {
                $final_data[$key][$value['result']][] = $value['result'];
            }
        }

        foreach ($final_data as $key => $value_fix) {
            foreach ($value_fix as $index2 => $value) {
                $final_data_fix[$key][$index2] = count($value);
            }
        }

        $merged = array();
        foreach ($final_data_fix as $a) {                             // iterate both arrays
            foreach ($a as $key => $value) {                     // iterate all keys+values
                $merged[$key] = $value + ($merged[$key] ?? 0);   // merge and add
            }
        }

        $chart = $prodi_id ? $final_data_fix[$prodi_id] : $merged;
        $dataChart = [
            'labels' => array_keys($chart),
            'value_charts' => array_values($chart),
            'barLabels' => array_keys($newCharts),
            'barValues' => array_values($newCharts),
            'barAngkatan' => array_values($angkatan),
        ];
        $this->emit('changeData', [
            'dataChart' => $dataChart,
            'prodi_id' => $prodi_id,
        ]);
        return [
            'members' => $members,
            'labels' => $labels,
            'role' => $role,
            'dataChart' => $dataChart,
            'data' => $datas,
        ];
    }
}
