<?php

namespace App\Charts;

use App\Http\Controllers\NaiveBayes as ControllersNaiveBayes;
use App\Http\Controllers\NaiveBayesClasifier;
use App\Models\DataMahasiswa;
use App\Models\DataSet;
use App\Models\User;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use Phpml\Classification\NaiveBayes;

class SampleChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        if ($request->user_id) {
            $user = User::find($request->user_id);
            $data_mahasiswa = DataMahasiswa::all();

            if ($request->prodi_id) {
                $data_mahasiswa = DataMahasiswa::where('data_prodi_id', $request->prodi_id)->get();
            }
            if ($user) {
                if ($user->role->role_type == 'member') {
                    $data_mahasiswa = DataMahasiswa::where('data_prodi_id', $user->dataProdi->id)->get();
                }
            }

            $labelValue = [];
            $angkatans = [];

            $total = [];
            foreach ($data_mahasiswa as $key => $value) {
                $keyData = $value->dataProdi->id . '-' . $value->angkatan;
                $labelValue[$keyData] = $value->dataProdi->nama_prodi . '_' . $keyData;
                $angkatans[$keyData] = $value->angkatan;
                if (isset($total[$keyData])) {
                    $total[$keyData] = $total[$keyData] + 1;
                } else {
                    $total[$keyData] = 1;
                }
            }

            $newLabels = [];

            foreach ($labelValue as $key => $value) {
                $newLabels[] =  explode('_', $value)[0] . '-' . explode('-', $value)[1];
            }

            return Chartisan::build()
                ->labels($newLabels)
                // ->dataset('Angkatan', array_values($angkatans));
                ->dataset('Sample', array_values($total));
        }

        $samples = [];
        $datas = [];
        $members = [];
        $labels = ['Angkatan', 'Tanggal Masuk', 'Tanggal Yudisium', 'Lama Kuliah', 'Prodi', 'Status', 'IPK', 'SKS'];
        $newLable = [];
        $newSample = [];
        $data_mahasiswa = DataMahasiswa::where('data_prodi_id', $request->prodi_id)->get();
        $data_set = DataSet::all();


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
        $classifier = new ControllersNaiveBayes($samples, $labels);
        $clasification = new NaiveBayesClasifier();
        $clasification->train($newSample, $newLable);

        $total = [];
        $labels = [];
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
            $labels[$value->dataProdi->id][] = [
                'prodi' => $value->dataProdi->nama_prodi,
                'result' => $result
            ];
        }

        $final_data = [];
        $final_label = [];
        $final_data_fix = [];
        foreach ($labels as $key => $label) {
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

        return Chartisan::build()
            ->labels(array_keys($final_data_fix[$request->prodi_id]))
            // ->dataset('Angkatan', array_values($angkatans));
            ->dataset('Sample', array_values($final_data_fix[$request->prodi_id]))
            ->extra(array_values($final_data_fix[$request->prodi_id]));
    }
}
