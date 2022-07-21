<?php

namespace App\Http\Livewire\Master;

use App\Models\DataProdi;
use App\Models\DataSet;
use Livewire\Component;


class DataSetController extends Component
{

    public $tbl_data_sets_id;
    public $angkatan;
    public $jenjang;
    public $nama_prodi;
    public $type_kelas;
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
            'jenjang'  => $this->jenjang,
            'nama_prodi'  => $this->nama_prodi,
            'type_kelas'  => $this->type_kelas,
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
            'jenjang'  => $this->jenjang,
            'nama_prodi'  => $this->nama_prodi,
            'type_kelas'  => $this->type_kelas,
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
            'jenjang'  => 'required',
            'nama_prodi'  => 'required',
            'type_kelas'  => 'required',
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
        $this->jenjang = $row->jenjang;
        $this->nama_prodi = $row->nama_prodi;
        $this->type_kelas = $row->type_kelas;
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
        $this->jenjang = null;
        $this->nama_prodi = null;
        $this->type_kelas = null;
        $this->status = null;
        $this->ipk = null;
        $this->sks = null;
        $this->target = null;
        $this->form = true;
        $this->form_active = false;
        $this->update_mode = false;
        $this->modal = false;
    }
}
