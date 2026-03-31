<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Persiapan integrasi Midtrans: kolom order_id, transaction_id, payment_type;
     * metode & status diubah ke string agar bisa menyimpan nilai Midtrans.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // SQLite menyimpan enum sebagai CHECK constraint, jadi perlu rebuild table agar metode/status bisa menyimpan 'midtrans'
        if ($driver === 'sqlite') {
            Schema::disableForeignKeyConstraints();

            // Buat tabel baru dengan metode/status bertipe string + kolom Midtrans
            Schema::create('tb_pembayaran_new', function (Blueprint $table) {
                $table->id('id_pembayaran');
                $table->unsignedBigInteger('id_parkir');
                $table->string('order_id', 64)->nullable();
                $table->string('transaction_id', 64)->nullable();
                $table->string('payment_type', 32)->nullable();
                $table->decimal('nominal', 10, 0);
                $table->string('metode', 50)->default('manual');
                $table->string('status', 50)->default('pending');
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('id_user')->nullable();
                $table->dateTime('waktu_pembayaran')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('id_parkir')->references('id_parkir')->on('tb_transaksi')->onDelete('cascade');
                $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('set null');
            });

            // Migrasikan data lama (kolom midtrans di-set NULL)
            DB::table('tb_pembayaran_new')->insertUsing(
                [
                    'id_pembayaran',
                    'id_parkir',
                    'order_id',
                    'transaction_id',
                    'payment_type',
                    'nominal',
                    'metode',
                    'status',
                    'keterangan',
                    'id_user',
                    'waktu_pembayaran',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                DB::table('tb_pembayaran')->selectRaw("
                    id_pembayaran,
                    id_parkir,
                    NULL as order_id,
                    NULL as transaction_id,
                    NULL as payment_type,
                    nominal,
                    metode,
                    status,
                    keterangan,
                    id_user,
                    waktu_pembayaran,
                    created_at,
                    updated_at,
                    deleted_at
                ")
            );

            Schema::drop('tb_pembayaran');
            Schema::rename('tb_pembayaran_new', 'tb_pembayaran');
            Schema::enableForeignKeyConstraints();

            return;
        }

        Schema::table('tb_pembayaran', function (Blueprint $table) {
            $table->string('order_id', 64)->nullable()->after('id_parkir');
            $table->string('transaction_id', 64)->nullable()->after('order_id');
            $table->string('payment_type', 32)->nullable()->after('transaction_id');
        });

        // Ubah enum ke string (MySQL) agar bisa nilai: manual, qr_scan, midtrans, dll
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE tb_pembayaran MODIFY metode VARCHAR(50) DEFAULT \'manual\'');
            DB::statement('ALTER TABLE tb_pembayaran MODIFY status VARCHAR(50) DEFAULT \'pending\'');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            Schema::disableForeignKeyConstraints();

            Schema::create('tb_pembayaran_old', function (Blueprint $table) {
                $table->id('id_pembayaran');
                $table->unsignedBigInteger('id_parkir');
                $table->decimal('nominal', 10, 0);
                $table->enum('metode', ['manual', 'qr_scan'])->default('manual');
                $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('id_user')->nullable();
                $table->dateTime('waktu_pembayaran')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('id_parkir')->references('id_parkir')->on('tb_transaksi')->onDelete('cascade');
                $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('set null');
            });

            DB::table('tb_pembayaran_old')->insertUsing(
                [
                    'id_pembayaran',
                    'id_parkir',
                    'nominal',
                    'metode',
                    'status',
                    'keterangan',
                    'id_user',
                    'waktu_pembayaran',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                DB::table('tb_pembayaran')->select([
                    'id_pembayaran',
                    'id_parkir',
                    'nominal',
                    'metode',
                    'status',
                    'keterangan',
                    'id_user',
                    'waktu_pembayaran',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ])
            );

            Schema::drop('tb_pembayaran');
            Schema::rename('tb_pembayaran_old', 'tb_pembayaran');
            Schema::enableForeignKeyConstraints();

            return;
        }

        Schema::table('tb_pembayaran', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'transaction_id', 'payment_type']);
        });

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE tb_pembayaran MODIFY metode ENUM('manual', 'qr_scan') DEFAULT 'manual'");
            DB::statement("ALTER TABLE tb_pembayaran MODIFY status ENUM('pending', 'berhasil', 'gagal') DEFAULT 'pending'");
        }
    }
};
