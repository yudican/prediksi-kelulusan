<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-capitalize">
                        <a href="{{route('dashboard')}}">
                            <span><i class="fas fa-arrow-left mr-3 text-capitalize"></i>Data Perhitungan</span>
                        </a>
                    </h4>
                </div>
            </div>
        </div>
        @if (in_array($role,['superadmin','admin']))
        <div class="col-md-12">
            <div class="card">
                <div class="card-body row">
                    <div class="col-md-6">
                        <x-select name="prodi_id" label="Pilih Prodi">
                            <option value="0">Semua Prodi</option>
                            @foreach ($data_prodi as $prodi)
                            <option value="{{$prodi->id}}">{{$prodi->nama_prodi}}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-6">
                        <x-select name="angkatan" label="Pilih Angkatan">
                            <option value="0">Semua Angkatan</option>
                            @for ($i = 2015; $i <= 2022; $i++) <option value="{{$i}}">{{$i}}</option>
                                @endfor
                        </x-select>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-light">
                        <thead class="thead-light">
                            <tr>
                                @foreach ($labels as $key => $label)
                                <td rowspan="2">{{$label}}</td>
                                @endforeach
                                <td colspan="2" class="text-center">Target</td>
                            </tr>
                            @foreach ($data as $key => $item)
                            @if ($key < 1) <tr class="text-center">
                                @foreach ($item['perhitungan'] as $key => $children)
                                <td>{{$key}}</td>
                                @endforeach
                                </tr>
                                @endif
                                @endforeach

                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                            <tr>
                                @foreach ($item['user'] as $user)
                                <td>{{$user}}</td>
                                @endforeach
                                @foreach ($item['perhitungan'] as $key => $perhitungan)
                                <td class="text-center">{{$perhitungan}}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>