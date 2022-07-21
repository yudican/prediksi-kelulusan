<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-capitalize">
                        <a href="{{route('dashboard')}}">
                            <span><i class="fas fa-arrow-left mr-3 text-capitalize"></i>Hasil Perhitungan</span>
                        </a>
                    </h4>
                </div>
            </div>
        </div>
        @if (in_array($role,['superadmin','admin']))
        <div class="col-md-12">
            <div class="card">
                <div class="card-body row">
                    <div class="col-md-10">
                        <x-select name="prodi_id">
                            <option value="">Semua Prodi</option>
                            @foreach ($data_prodi as $prodi)
                            <option value="{{$prodi->id}}">{{$prodi->nama_prodi}}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success mt-2" wire:click="export">Export</button>
                    </div>

                </div>
            </div>
        </div>
        @endif

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-light">
                        <thead class="thead-light">
                            <tr>
                                <td>NIM</td>
                                <td>Nama</td>
                                @foreach ($labels as $key => $label)
                                <td>{{$label}}</td>
                                @endforeach
                                <td>Hasil</td>
                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                            <tr>
                                @foreach (array_merge($item['user_detail'],$item['user']) as $user)
                                <td>{{$user}}</td>
                                @endforeach
                                <td>{{$item['result']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script>
    <!-- Chartisan -->
    <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
    <script>
        document.addEventListener('livewire:load', function(e) {
                const chart = new Chartisan({
                    el: '#chart',
                    url: "https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}",
                });

                window.livewire.on('changeData', (data) => {
                    const url = data ? "https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}&prodi_id="+data :"https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}"
                    const chart = new Chartisan({
                        el: '#chart',
                        url,
                    });
                });
            });
    </script>
    @endpush
</div>