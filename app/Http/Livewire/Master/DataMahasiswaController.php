<?php

namespace App\Http\Livewire\Master;

use App\Imports\DataMahasiswaImport;
use App\Models\DataMahasiswa;
use App\Models\DataProdi;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class DataMahasiswaController extends Component
{
    use WithFileUploads;
    public $tbl_data_mahasiswa_id;
    public $nama;
    public $nim;
    public $angkatan;
    public $tgl_masuk;
    public $tgl_yudisium;
    public $lama_kuliah = 0;
    public $data_prodi_id;
    public $jenis_kelamin;
    public $agama;
    public $status;
    public $ipk;
    public $sks;
    public $penghasilan;

    public $data_mhs;
    public $data_mhs_path;



    public $route_name = null;

    public $form_active = false;
    public $form = true;
    public $update_mode = false;
    public $modal = false;

    protected $listeners = ['getDataDataMahasiswaById', 'getDataMahasiswaId'];

    public function mount()
    {
        $this->route_name = request()->route()->getName();
    }

    public function render()
    {
        if ($this->tgl_masuk && $this->tgl_yudisium) {
            $lama_kuliah = $this->year_diff($this->tgl_masuk, $this->tgl_yudisium);
            $this->lama_kuliah = $lama_kuliah;
        }
        return view('livewire.master.tbl-data-mahasiswa', [
            'items' => DataMahasiswa::all(),
            'data_prodi' => DataProdi::all()
        ]);
    }

    public function store()
    {
        $this->_validate();

        $data = [
            'nama'  => $this->nama,
            'nim'  => $this->nim,
            'angkatan'  => $this->angkatan,
            'tgl_masuk'  => $this->tgl_masuk,
            'tgl_yudisium'  => $this->tgl_yudisium,
            'lama_kuliah'  => $this->lama_kuliah,
            'data_prodi_id'  => $this->data_prodi_id,
            'jenis_kelamin'  => $this->jenis_kelamin,
            'agama'  => $this->agama,
            'status'  => $this->status,
            'ipk'  => $this->ipk,
            'sks'  => $this->sks,
            'penghasilan'  => $this->penghasilan
        ];

        DataMahasiswa::create($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function update()
    {
        $this->_validate();

        $data = [
            'nama'  => $this->nama,
            'nim'  => $this->nim,
            'angkatan'  => $this->angkatan,
            'tgl_masuk'  => $this->tgl_masuk,
            'tgl_yudisium'  => $this->tgl_yudisium,
            'lama_kuliah'  => $this->lama_kuliah,
            'data_prodi_id'  => $this->data_prodi_id,
            'jenis_kelamin'  => $this->jenis_kelamin,
            'agama'  => $this->agama,
            'status'  => $this->status,
            'ipk'  => $this->ipk,
            'sks'  => $this->sks,
            'penghasilan'  => $this->penghasilan
        ];
        $row = DataMahasiswa::find($this->tbl_data_mahasiswa_id);



        $row->update($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    }

    public function delete()
    {
        DataMahasiswa::find($this->tbl_data_mahasiswa_id)->delete();

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    }

    public function _validate()
    {
        $rule = [
            'nama'  => 'required',
            'nim'  => 'required',
            'angkatan'  => 'required',
            'tgl_masuk'  => 'required',
            'data_prodi_id'  => 'required',
            'jenis_kelamin'  => 'required',
            'agama'  => 'required',
            'status'  => 'required',
            'ipk'  => 'required',
            'sks'  => 'required',
            'penghasilan'  => 'required'
        ];

        return $this->validate($rule);
    }

    public function getDataDataMahasiswaById($tbl_data_mahasiswa_id)
    {
        $this->_reset();
        $row = DataMahasiswa::find($tbl_data_mahasiswa_id);
        $this->tbl_data_mahasiswa_id = $row->id;
        $this->nama = $row->nama;
        $this->nim = $row->nim;
        $this->angkatan = $row->angkatan;
        $this->tgl_masuk = date('Y-m-d', strtotime($row->tgl_masuk));
        $this->tgl_yudisium = date('Y-m-d', strtotime($row->tgl_yudisium));
        $this->lama_kuliah = $row->lama_kuliah;
        $this->data_prodi_id = $row->data_prodi_id;
        $this->jenis_kelamin = $row->jenis_kelamin;
        $this->agama = $row->agama;
        $this->status = $row->status;
        $this->ipk = $row->ipk;
        $this->sks = $row->sks;
        $this->penghasilan = $row->penghasilan;
        if ($this->form) {
            $this->form_active = true;
            $this->emit('loadForm');
        }
        if ($this->modal) {
            $this->emit('showModal');
        }
        $this->update_mode = true;
    }

    public function getDataMahasiswaId($tbl_data_mahasiswa_id)
    {
        $row = DataMahasiswa::find($tbl_data_mahasiswa_id);
        $this->tbl_data_mahasiswa_id = $row->id;
    }

    public function toggleForm($form)
    {
        $this->_reset();
        $this->form_active = $form;
        $this->emit('loadForm');
    }

    public function showModal()
    {
        $this->_reset();
        $this->emit('showModal');
    }
    public function showModalImport()
    {
        $this->emit('showModalImport', 'show');
    }

    public function saveImport()
    {
        $this->validate(['data_mhs_path' => 'required']);
        try {
            DB::beginTransaction();
            Excel::import(new DataMahasiswaImport, $this->data_mhs_path);
            DB::commit();
            $this->_reset();
            return $this->emit('showAlert', ['msg' => 'Data Berhasil Diimport']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->emit('showAlertError', ['msg' => 'Data Gagal Diimport']);
        }
    }

    public function _reset()
    {
        $this->emit('closeModal');
        $this->emit('refreshTable');
        $this->emit('showModalImport', 'hide');
        $this->tbl_data_mahasiswa_id = null;
        $this->nama = null;
        $this->nim = null;
        $this->angkatan = null;
        $this->tgl_masuk = null;
        $this->tgl_yudisium = null;
        $this->lama_kuliah = 0;
        $this->data_prodi_id = null;
        $this->jenis_kelamin = null;
        $this->agama = null;
        $this->status = null;
        $this->ipk = null;
        $this->sks = null;
        $this->data_mhs = null;
        $this->data_mhs_path = null;
        $this->penghasilan = null;
        $this->form = true;
        $this->form_active = false;
        $this->update_mode = false;
        $this->modal = false;
    }

    // calculate year difference between two dates
    public function year_diff($date1, $date2)
    {
        $diff = abs(strtotime($date2) - strtotime($date1));
        return floor($diff / (365 * 60 * 60 * 24));
    }
}
