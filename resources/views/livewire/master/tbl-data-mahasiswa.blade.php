<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-capitalize">
                        <a href="{{route('dashboard')}}">
                            <span><i class="fas fa-arrow-left mr-3"></i>data mahasiswa</span>
                        </a>
                        <div class="pull-right">
                            @if ($form_active)
                            <button class="btn btn-danger btn-sm" wire:click="toggleForm(false)"><i class="fas fa-times"></i> Cancel</button>
                            @else
                            @if (auth()->user()->hasTeamPermission($curteam, $route_name.':create'))
                            <button class="btn btn-success btn-sm" wire:click="showModalImport"><i class="fas fa-excel"></i>Import</button>
                            <button class="btn btn-primary btn-sm" wire:click="{{$modal ? 'showModal' : 'toggleForm(true)'}}"><i class="fas fa-plus"></i> Add New</button>
                            @endif
                            @endif
                        </div>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            @if ($form_active)
            <div class="card">
                <div class="card-body">
                    <x-text-field type="text" name="nama" label="Nama" />
                    <x-text-field type="number" name="nim" label="Nim" />
                    <x-select name="angkatan" label="Angkatan">
                        <option value="">Select Angkatan</option>
                        @for ($i = 2015; $i <= 2022; $i++) <option value="{{$i}}">{{$i}}</option>
                            @endfor
                    </x-select>

                    <x-select name="data_prodi_id" label="Prodi">
                        <option value="">Select Prodi</option>
                        @foreach ($data_prodi as $prodi)
                        <option value="{{$prodi->id}}">{{$prodi->nama_prodi}}</option>
                        @endforeach
                    </x-select>
                    <x-text-field type="date" name="tgl_masuk" label="Tanggal Masuk" />
                    <x-text-field type="date" name="tgl_yudisium" label="Tanggal Yudisium" />
                    <x-text-field type="text" name="lama_kuliah" label="Lama Kuliah" readonly />
                    <x-select name="jenis_kelamin" label="Jenis Kelamin">
                        <option value="">Select Jenis Kelamin</option>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </x-select>
                    <x-select name="agama" label="Agama">
                        <option value="">Select Agama</option>
                        <option value="islam">islam</option>
                        <option value="hindu">hindu</option>
                        <option value="budha">budha</option>
                        <option value="katolik">katolik</option>
                        <option value="protestan">protestan</option>
                        <option value="konghucu">konghucu</option>
                    </x-select>
                    <x-select name="status" label="Status">
                        <option value="">Select Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Revisi">Revisi</option>
                        <option value="Lulus">Lulus</option>
                    </x-select>
                    <x-text-field type="text" name="ipk" label="Ipk" />
                    <x-text-field type="number" name="sks" label="Sks" />
                    <x-text-field type="text" name="penghasilan" label="Lama Kuliah" />

                    <div class="form-group">
                        <button class="btn btn-primary pull-right" wire:click="{{$update_mode ? 'update' : 'store'}}">Simpan</button>
                    </div>
                </div>
            </div>
            @else
            <livewire:table.data-mahasiswa-table params="{{$route_name}}" />
            @endif

        </div>

        {{-- Modal confirm --}}
        <div id="confirm-modal" wire:ignore.self class="modal fade" tabindex="-1" permission="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog" permission="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">Konfirmasi Hapus</h5>
                    </div>
                    <div class="modal-body">
                        <p>Apakah anda yakin hapus data ini.?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" wire:click='delete' class="btn btn-danger btn-sm"><i class="fa fa-check pr-2"></i>Ya, Hapus</button>
                        <button class="btn btn-primary btn-sm" wire:click='_reset'><i class="fa fa-times pr-2"></i>Batal</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="import-modal" wire:ignore.self class="modal fade" tabindex="-1" permission="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog" permission="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="my-modal-title">Import Data</h5>
                    </div>
                    <div class="modal-body">
                        <x-input-file file="{{$data_mhs}}" path="{{optional($data_mhs_path)->getClientOriginalName()}}" name="data_mhs_path" label="Data Mahasiswa" />
                    </div>
                    <div class="modal-footer">
                        <button type="submit" wire:click='saveImport' class="btn btn-success btn-sm"><i class="fa fa-check pr-2"></i>Simpan</button>
                        <button class="btn btn-primary btn-sm" wire:click='_reset'><i class="fa fa-times pr-2"></i>Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')



    <script>
        document.addEventListener('livewire:load', function(e) {
            window.livewire.on('loadForm', (data) => {
                
                
            });

            window.livewire.on('closeModal', (data) => {
                $('#confirm-modal').modal('hide')
            });
            window.livewire.on('showModalImport', (data) => {
                $('#import-modal').modal(data)
            });
        })
    </script>
    @endpush
</div>