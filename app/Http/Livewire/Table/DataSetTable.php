<?php

namespace App\Http\Livewire\Table;

use App\Models\HideableColumn;
use App\Models\DataSet;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Column;
use App\Http\Livewire\Table\LivewireDatatable;

class DataSetTable extends LivewireDatatable
{
    protected $listeners = ['refreshTable'];
    public $hideable = 'select';
    public $table_name = 'tbl_data_sets';
    public $hide = [];

    public function builder()
    {
        return DataSet::query();
    }

    public function columns()
    {
        $this->hide = HideableColumn::where(['table_name' => $this->table_name, 'user_id' => auth()->user()->id])->pluck('column_name')->toArray();
        return [
            Column::name('id')->label('No.'),
            Column::name('angkatan')->label('Angkatan')->searchable(),
            Column::name('nama_prodi')->label('Nama Prodi')->searchable(),
            Column::name('tgl_masuk')->label('Tanggal Masuk')->searchable(),
            Column::name('tgl_yudisium')->label('Tanggal Yudisium')->searchable(),
            Column::name('lama_kuliah')->label('Lama Kuliah')->searchable(),
            Column::name('status')->label('Status')->searchable(),
            Column::name('ipk')->label('Ipk')->searchable(),
            Column::name('sks')->label('Sks')->searchable(),
            Column::name('target')->label('Target')->searchable(),

            Column::callback(['id'], function ($id) {
                return view('livewire.components.action-button', [
                    'id' => $id,
                    'segment' => $this->params
                ]);
            })->label(__('Aksi')),
        ];
    }

    public function getDataById($id)
    {
        $this->emit('getDataDataSetById', $id);
    }

    public function getId($id)
    {
        $this->emit('getDataSetId', $id);
    }

    public function refreshTable()
    {
        $this->emit('refreshLivewireDatatable');
    }

    public function toggle($index)
    {
        if ($this->sort == $index) {
            $this->initialiseSort();
        }

        $column = HideableColumn::where([
            'table_name' => $this->table_name,
            'column_name' => $this->columns[$index]['name'],
            'index' => $index,
            'user_id' => auth()->user()->id
        ])->first();

        if (!$this->columns[$index]['hidden']) {
            unset($this->activeSelectFilters[$index]);
        }

        $this->columns[$index]['hidden'] = !$this->columns[$index]['hidden'];

        if (!$column) {
            HideableColumn::updateOrCreate([
                'table_name' => $this->table_name,
                'column_name' => $this->columns[$index]['name'],
                'index' => $index,
                'user_id' => auth()->user()->id
            ]);
        } else {
            $column->delete();
        }
    }
}
