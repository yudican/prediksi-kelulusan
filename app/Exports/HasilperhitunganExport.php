<?php

namespace App\Exports;

use App\Models\DataMahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HasilperhitunganExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $datas = [];
    protected $title = null;

    public function __construct($datas = [], $title = 'Menu List')
    {
        $this->datas = $datas;
        $this->title = $title;
    }

    public function query()
    {
        return DataMahasiswa::query();
    }

    public function map($row): array
    {
        return [
            $row->nim,
            $row->nama,
            $row->angkatan,
            $row->jenjang,
            $row->dataProdi->nama_prodi,
            $row->type_kelas,
            $row->status,
            $row->ipk,
            $row->sks,
            $this->datas[$row->nim][0]
        ];
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama',
            'Angkatan',
            'Jenjang',
            'Prodi',
            'Type Kelas',
            'Status',
            'IPK',
            'SKS',
            'Hasil'
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
}
