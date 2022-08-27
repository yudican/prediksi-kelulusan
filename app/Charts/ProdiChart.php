<?php

namespace App\Charts;

use App\Models\DataMahasiswa;
use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Http\Controllers\NaiveBayes;
use App\Http\Controllers\NaiveBayesClasifier;
use App\Models\DataSet;

class ProdiChart extends BaseChart
{
  /**
   * Handles the HTTP request for the given chart.
   * It must always return an instance of Chartisan
   * and never a string or an array.
   */
  public function handler(Request $request): Chartisan
  {
    $samples = [];
    $datas = [];
    $members = [];
    $labels = ['Angkatan', 'Tanggal Masuk', 'Tanggal Yudisium', 'Lama Kuliah', 'Prodi', 'Status', 'IPK', 'SKS'];
    $newLable = [];
    $newSample = [];
    $data_mahasiswa = DataMahasiswa::where('data_prodi_id', $request->prodi_id)->get();
    if ($request->prodi_id == 'all') {
      $data_mahasiswa = DataMahasiswa::all();
    }
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
    $classifier = new NaiveBayes($samples, $labels);
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
      ->labels(array_keys($final_data_fix))
      // ->dataset('Angkatan', array_values($angkatans));
      ->dataset('Sample', array_values($final_data_fix));
  }
}
