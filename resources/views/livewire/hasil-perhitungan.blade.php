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
                        <select wire:model="prodi_id" class="form-control">
                            <option value="">Semua Prodi</option>
                            @foreach ($data_prodi as $prodi)
                            <option value="{{$prodi->id}}">{{$prodi->nama_prodi}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success mt-2" wire:click="export">Export</button>
                    </div>

                </div>
            </div>
        </div>
        @endif

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="header-title">Jumlah Data Mahasiswa</h1>
                </div>
                <div class="card-body">
                    {{-- <div id="chart" style="height: 300px;"></div> --}}
                    <canvas id="mhs-chart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h1 class="header-title">Persentase Kelulusan</h1>
                </div>
                <div class="card-body">
                    <canvas id="prodi-chart" style="height: 300px;"></canvas>
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
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <!-- Chartisan -->
    <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script>
    <script>
        document.addEventListener('livewire:load', function(e) {
            function loadChart(dataChart) {
                console.log(dataChart,'dataChart')
                var pieChart = document.getElementById('prodi-chart').getContext('2d')
                var myPieChart = new Chart(pieChart, {
                    type: 'pie',
                    data: {
                        datasets: [{
                            data: dataChart.value_charts,
                            backgroundColor :["#1d7af3","#f3545d","#fdaf4b",'#4299e1','#FE0045','#C07EF1','#67C560','#ECC94B'],
                            borderWidth: 0
                        }],
                        labels: dataChart.labels 
                    },
                    options : {
                        responsive: true, 
                        maintainAspectRatio: false,
                        legend: {
                            position : 'bottom',
                            labels : {
                                fontColor: 'rgb(154, 154, 154)',
                                fontSize: 11,
                                usePointStyle : true,
                                padding: 20
                            }
                        },
                        pieceLabel: {
                            render: 'percentage',
                            fontColor: 'white',
                            fontSize: 14,
                        },
                        tooltips: false,
                        layout: {
                            padding: {
                                left: 20,
                                right: 20,
                                top: 20,
                                bottom: 20
                            }
                        }
                    }
                })
            }
            function loadChartBar(dataChartBar=[]) {
                console.log(dataChartBar,'dataChartBar')
                var barChart = document.getElementById('mhs-chart').getContext('2d')
                const datac = {
                labels: ['label1', 'label2', 'label3', 'label4', 'label5', 'label6', 'label7'],
                datasets: [{
                    label: 'My First Dataset',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderWidth: 1
                }]
                };
                var myBarChart = new Chart(barChart, datac)
            }
            const dataChart = @json($dataChart);
            loadChart(dataChart);
            loadChartBar(dataChart);

            const chart = new Chartisan({
                el: '#chart',
                url: "https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}",
            });

            window.livewire.on('changeData', async (data) =>  {
                await loadChart(data.dataChart);
                const url = await data.prodi_id ? "https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}&prodi_id="+data.prodi_id :"https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}"
                const chart = await new Chartisan({
                    el: '#chart',
                    url,

                });
            });
        });
    </script>
    @endpush
</div>