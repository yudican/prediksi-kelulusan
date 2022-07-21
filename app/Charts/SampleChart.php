<?php

namespace App\Charts;

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
}
