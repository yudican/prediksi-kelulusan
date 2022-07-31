<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataMahasiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nim');
            $table->string('angkatan');
            $table->date('tgl_masuk');
            $table->date('tgl_yudisium')->nullable()->default(now());
            $table->integer('lama_kuliah')->nullable()->default(0);
            $table->foreignId('data_prodi_id');
            $table->string('jenis_kelamin');
            $table->string('agama');
            $table->string('status');
            $table->string('ipk');
            $table->integer('sks');
            $table->integer('penghasilan');
            $table->foreign('data_prodi_id')->references('id')->on('data_prodi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_mahasiswa');
    }
}
