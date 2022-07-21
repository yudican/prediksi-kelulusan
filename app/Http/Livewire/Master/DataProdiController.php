<?php

namespace App\Http\Livewire\Master;

use App\Models\DataProdi;
use App\Models\User;
use Livewire\Component;


class DataProdiController extends Component
{

    public $tbl_data_prodi_id;
    public $kode_prodi;
    public $nama_prodi;
    public $user_id;



    public $route_name = null;

    public $form_active = false;
    public $form = false;
    public $update_mode = false;
    public $modal = true;

    protected $listeners = ['getDataDataProdiById', 'getDataProdiId'];

    public function mount()
    {
        $this->route_name = request()->route()->getName();
    }

    public function render()
    {
        $user = User::whereHas('roles', function ($query) {
            return $query->where('role_type', 'member');
        })->whereDoesntHave('dataProdi');
        if ($this->update_mode) {
            $user->orWhereHas('dataProdi', function ($query) {
                return $query->where('user_id', $this->user_id);
            });
        }
        return view('livewire.master.tbl-data-prodi', [
            'items' => DataProdi::all(),
            'users' => $user->get()
        ]);
    }

    public function store()
    {
        $this->_validate();

        $data = [
            'kode_prodi'  => $this->kode_prodi,
            'nama_prodi'  => $this->nama_prodi,
            'user_id'  => $this->user_id
        ];

        DataProdi::create($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function update()
    {
        $this->_validate();

        $data = [
            'kode_prodi'  => $this->kode_prodi,
            'nama_prodi'  => $this->nama_prodi,
            'user_id'  => $this->user_id
        ];
        $row = DataProdi::find($this->tbl_data_prodi_id);



        $row->update($data);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    }

    public function delete()
    {
        DataProdi::find($this->tbl_data_prodi_id)->delete();

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    }

    public function _validate()
    {
        $rule = [
            'kode_prodi'  => 'required|unique:data_prodi,kode_prodi',
            'nama_prodi'  => 'required',
            'user_id'  => 'required'
        ];

        return $this->validate($rule);
    }

    public function getDataDataProdiById($tbl_data_prodi_id)
    {
        $this->_reset();
        $row = DataProdi::find($tbl_data_prodi_id);
        $this->tbl_data_prodi_id = $row->id;
        $this->kode_prodi = $row->kode_prodi;
        $this->nama_prodi = $row->nama_prodi;
        $this->user_id = $row->user_id;
        if ($this->form) {
            $this->form_active = true;
            $this->emit('loadForm');
        }
        if ($this->modal) {
            $this->emit('showModal');
        }
        $this->update_mode = true;
    }

    public function getDataProdiId($tbl_data_prodi_id)
    {
        $row = DataProdi::find($tbl_data_prodi_id);
        $this->tbl_data_prodi_id = $row->id;
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
        $this->tbl_data_prodi_id = null;
        $this->kode_prodi = null;
        $this->nama_prodi = null;
        $this->user_id = null;
        $this->form = false;
        $this->form_active = false;
        $this->update_mode = false;
        $this->modal = true;
    }
}
