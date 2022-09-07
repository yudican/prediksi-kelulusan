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
                    <h1 class="header-title">JumlaH Data Mahasiswa</h1>
                </div>
                <div class="card-body">
                    <div id="chart" style="height: 300px;"></div>
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
                    // hooks: new ChartisanHooks()
                    // .colors(['#4299E1','#FE0045','#C07EF1','#67C560','#ECC94B'])
                    //     .datasets('bar')
                    //     .axis(true)
                });
            });
    </script>

    @endpush
</div>