<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-capitalize">
                        <a href="{{route('dashboard')}}">
                            <span><i class="fas fa-arrow-left mr-3"></i>data sets</span>
                        </a>
                        <div class="pull-right">
                            @if ($form_active)
                            <button class="btn btn-danger btn-sm" wire:click="toggleForm(false)"><i class="fas fa-times"></i> Cancel</button>
                            @else
                            @if (auth()->user()->hasTeamPermission($curteam, $route_name.':create'))
                            <button class="btn btn-primary btn-sm" wire:click="{{$modal ? 'showModal' : 'toggleForm(true)'}}"><i class="fas fa-plus"></i> Add
                                New</button>
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
                    <x-select name="angkatan" label="Angkatan">
                        <option value="">Select Angkatan</option>
                        @for ($i = 2015; $i <= 2022; $i++) <option value="{{$i}}">{{$i}}</option>
                            @endfor
                    </x-select>
                    <x-select name="nama_prodi" label="Prodi">
                        <option value="">Select Prodi</option>
                        @foreach ($data_prodi as $prodi)
                        <option value="{{$prodi->nama_prodi}}">{{$prodi->nama_prodi}}</option>
                        @endforeach
                    </x-select>
                    <x-select name="status" label="Status">
                        <option value="">Select Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Revisi">Revisi</option>
                    </x-select>
                    <x-text-field type="text" name="ipk" label="Ipk" />
                    <x-text-field type="text" name="sks" label="Sks" />
                    <x-text-field type="date" name="tgl_masuk" label="Tanggal Masuk" />
                    <x-text-field type="date" name="tgl_yudisium" label="Tanggal Yudisium" />
                    <x-text-field type="text" name="lama_kuliah" label="Lama Kuliah" readonly />
                    <x-select name="target" label="Target">
                        <option value="">Select Target</option>
                        <option value="Tepat Waktu">Tepat Waktu</option>
                        <option value="Terlambat">Terlambat</option>
                    </x-select>

                    <div class="form-group">
                        <button class="btn btn-primary pull-right" wire:click="{{$update_mode ? 'update' : 'store'}}">Simpan</button>
                    </div>
                </div>
            </div>
            @else
            <livewire:table.data-set-table params="{{$route_name}}" />
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
    </div>
    @push('scripts')



    <script>
        document.addEventListener('livewire:load', function(e) {
            window.livewire.on('loadForm', (data) => {
                
                
            });

            window.livewire.on('closeModal', (data) => {
                $('#confirm-modal').modal('hide')
            });
        })
    </script>
    @endpush
</div>