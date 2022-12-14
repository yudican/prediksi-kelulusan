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
    {{-- <script src="https://unpkg.com/echarts/dist/echarts.min.js"></script> --}}
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <!-- Chartisan -->
    {{-- <script src="https://unpkg.com/@chartisan/echarts/dist/chartisan_echarts.js"></script> --}}
    <script>
        document.addEventListener('livewire:load', function(e) {
            function getValueChart(chart){
                const values = [];
                const labels = []
                chart.barAngkatan.forEach((angkatan) => {
                    labels[angkatan] = angkatan
                    values[angkatan] = chart.barLabels.map((label) => {
                        return chart.barValues.map((value) => {
                                return value[angkatan];
                            })
                    });
                });

                const newLabels = labels.filter((item) => item)
                let newValues = values.filter((item) => item)

                var results = [];
        
                for ( var i in newValues ) {
                    results.push( newValues[i][0] );
                }
                return {
                    labels: newLabels,
                    values: results
                }
            }
            function loadChart(dataChart) {
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
            function loadChartBar(dataChartBar) {
                console.log(dataChartBar)
                var barChart = document.getElementById('mhs-chart').getContext('2d')
                const color = ["#f3545d","#fdaf4b",'#4299e1','#FE0045','#C07EF1','#67C560','#ECC94B',"#1d7af3"];
                const charts = getValueChart(dataChartBar);
                const chartData = charts.values.map((items,index) => {
                        return {
                                label:charts.labels[index],
                                backgroundColor :color[index],
                                borderColor: 'rgb(23, 125, 255)',
                                data: items,
                            }
                    
                })
                var myBarChart = new Chart(barChart, {
                    type: 'bar',
                    data: {
                        labels: dataChartBar.barLabels,
                        datasets : chartData,
                    },
                    options: {
                        responsive: true, 
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        },
                    }
                });
            }
            const dataChart = @json($dataChart);
            loadChart(dataChart);
            loadChartBar(dataChart);

            // const chart = new Chartisan({
            //     el: '#chart',
            //     url: "https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}",
            // });

            window.livewire.on('changeData', async (data) =>  {
                await loadChart(data.dataChart);
                await loadChartBar(data.dataChart);
                // const url = await data.prodi_id ? "https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}&prodi_id="+data.prodi_id :"https://prediksi-kelulusan.stagging.my.id/api/chart/sample_chart?user_id={{Auth::user()->id}}"
                // const chart = await new Chartisan({
                //     el: '#chart',
                //     url,

                // });
            });
        });
    </script>
    @endpush
</div>