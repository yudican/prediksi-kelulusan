<?php

namespace App\Http\Livewire\Master;

use App\Models\DataProdi;
use App\Models\DataSet;
use Livewire\Component;


class DataSetController extends Component
{

    public $tbl_data_sets_id;
    public $angkatan;
    public $nama_prodi;
    public $tgl_masuk;
    public $tgl_yudisium;
    public $lama_kuliah;
    public $status;
    public $ipk;
    public $sks;
    public $target;



    public $route_name = null;

    public $form_active = false;
    public $form = true;
    public $update_mode = false;
    public $modal = false;

    protected $listeners = ['getDataDataSetById', 'getDataSetId'];

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
        return view('livewire.master.tbl-data-sets', [
            'items' => DataSet::all(),
            'data_prodi' => DataProdi::all()
        ]);
    }

    public function store()
    {
        $this->_validate();

        $data = [
            'angkatan'  => $this->angkatan,
            'tgl_masuk'  => $this->tgl_masuk,
            'tgl_yudisium'  => $this->tgl_yudisium,
            'lama_kuliah'  => $this->lama_kuliah,
            'nama_prodi'  => $this->nama_prodi,
            'status'  => $this->status,
            'ipk'  => $this->ipk,
            'sks'  => $this->sks,
            'target'  => $this->target
        ];

        DataSet::create($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function update()
    {
        $this->_validate();

        $data = [
            'angkatan'  => $this->angkatan,
            'tgl_masuk'  => $this->tgl_masuk,
            'tgl_yudisium'  => $this->tgl_yudisium,
            'lama_kuliah'  => $this->lama_kuliah,
            'nama_prodi'  => $this->nama_prodi,
            'status'  => $this->status,
            'ipk'  => $this->ipk,
            'sks'  => $this->sks,
            'target'  => $this->target
        ];
        $row = DataSet::find($this->tbl_data_sets_id);



        $row->update($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    }

    public function delete()
    {
        DataSet::find($this->tbl_data_sets_id)->delete();

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    }

    public function _validate()
    {
        $rule = [
            'angkatan'  => 'required',
            'tgl_masuk'  => 'required',
            'nama_prodi'  => 'required',
            'status'  => 'required',
            'ipk'  => 'required',
            'sks'  => 'required',
            'target'  => 'required'
        ];

        return $this->validate($rule);
    }

    public function getDataDataSetById($tbl_data_sets_id)
    {
        $this->_reset();
        $row = DataSet::find($tbl_data_sets_id);
        $this->tbl_data_sets_id = $row->id;
        $this->angkatan = $row->angkatan;
        $this->tgl_masuk = date('Y-m-d', strtotime($row->tgl_masuk));
        $this->tgl_yudisium = date('Y-m-d', strtotime($row->tgl_yudisium));
        $this->lama_kuliah = $row->lama_kuliah;
        $this->nama_prodi = $row->nama_prodi;
        $this->status = $row->status;
        $this->ipk = $row->ipk;
        $this->sks = $row->sks;
        $this->target = $row->target;
        if ($this->form) {
            $this->form_active = true;
            $this->emit('loadForm');
        }
        if ($this->modal) {
            $this->emit('showModal');
        }
        $this->update_mode = true;
    }

    public function getDataSetId($tbl_data_sets_id)
    {
        $row = DataSet::find($tbl_data_sets_id);
        $this->tbl_data_sets_id = $row->id;
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

    public function _reset()
    {
        $this->emit('closeModal');
        $this->emit('refreshTable');
        $this->tbl_data_sets_id = null;
        $this->angkatan = null;
        $this->tgl_masuk = null;
        $this->tgl_yudisium = null;
        $this->lama_kuliah = null;
        $this->nama_prodi = null;
        $this->status = null;
        $this->ipk = null;
        $this->sks = null;
        $this->target = null;
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
