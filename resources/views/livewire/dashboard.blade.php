<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">
                        Selamat Datang {{Auth::user()->name}} Di Aplikasi Prediksi Kelulusan
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-12">
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
    </div>

    @push('scripts')
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <script>
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
        function loadChartBar(dataChartBar) {
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
        loadChartBar(dataChart);
    </script>
    @endpush
</div>