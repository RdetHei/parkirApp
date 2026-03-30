<?php

namespace App\Console\Commands;

use App\Models\Kendaraan;
use App\Support\PlatNomorNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DedupKendaraanPlatNomor extends Command
{
    protected $signature = 'kendaraan:dedup-plat {--dry-run : Tampilkan perubahan tanpa mengubah data}';

    protected $description = 'Deduplicate tb_kendaraan rows by normalized plat_nomor (keep newest), then relink foreign keys';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Collect kendaraan dan normalisasi plat...');

        $groups = [];

        Kendaraan::withTrashed()
            ->select(['id_kendaraan', 'plat_nomor'])
            ->orderBy('id_kendaraan')
            ->chunkById(1000, function ($chunk) use (&$groups): void {
                foreach ($chunk as $k) {
                    $normalized = PlatNomorNormalizer::normalize((string) ($k->plat_nomor ?? ''));

                    if ($normalized === '') {
                        continue;
                    }

                    if (!isset($groups[$normalized])) {
                        $groups[$normalized] = [
                            'keepId' => (int) $k->id_kendaraan,
                            'ids' => [],
                        ];
                    }

                    $id = (int) $k->id_kendaraan;
                    $groups[$normalized]['ids'][] = $id;

                    if ($id > (int) $groups[$normalized]['keepId']) {
                        $groups[$normalized]['keepId'] = $id;
                    }
                }
            });

        $dupGroups = 0;
        $dupIdsTotal = 0;
        foreach ($groups as $meta) {
            if (count($meta['ids']) > 1) {
                $dupGroups++;
                $dupIdsTotal += count($meta['ids']) - 1;
            }
        }

        $this->info('Total unique plat (normalized): ' . count($groups));
        $this->info('Group duplikat: ' . $dupGroups);
        $this->info('Total baris kandidat untuk dihapus: ' . $dupIdsTotal);

        if ($dupGroups === 0) {
            $this->info('Tidak ada duplikasi yang perlu dirapikan.');
            return self::SUCCESS;
        }

        $this->warn($dryRun ? 'Mode DRY-RUN aktif (tidak ada perubahan dilakukan).' : 'Mode live: perubahan akan dilakukan.');

        $processed = 0;
        $deleted = 0;
        $canonicalizedSingletons = 0;

        foreach ($groups as $normalized => $meta) {
            $ids = $meta['ids'];

            // Canonicalize singleton group juga, supaya lookup berdasarkan plat normalisasi (raw DB) selalu match.
            if (count($ids) === 1) {
                $onlyId = (int) $ids[0];
                if ($dryRun) {
                    $processed++;
                    continue;
                }

                DB::table('tb_kendaraan')
                    ->where('id_kendaraan', $onlyId)
                    ->update(['plat_nomor' => $normalized]);

                $processed++;
                $canonicalizedSingletons++;
                continue;
            }

            $keepId = (int) $meta['keepId'];
            $duplicateIds = array_values(array_diff($ids, [$keepId]));

            $this->line("Norm: {$normalized} | keep: {$keepId} | duplicates: " . count($duplicateIds));

            if ($dryRun) {
                $processed++;
                continue;
            }

            DB::transaction(function () use ($normalized, $keepId, $duplicateIds): void {
                // Relink transaksi & reservasi agar FK tidak memicu cascade delete.
                DB::table('tb_transaksi')
                    ->whereIn('id_kendaraan', $duplicateIds)
                    ->update(['id_kendaraan' => $keepId]);

                DB::table('tb_parking_slot_reservations')
                    ->whereIn('id_kendaraan', $duplicateIds)
                    ->update(['id_kendaraan' => $keepId]);

                // Pastikan record yang dipertahankan memakai format plat normalisasi (canonical).
                Kendaraan::withTrashed()
                    ->where('id_kendaraan', $keepId)
                    ->update(['plat_nomor' => $normalized]);

                // Hapus duplikat secara force agar unique index pada plat_nomor tidak gagal karena baris soft-deleted.
                Kendaraan::withTrashed()
                    ->whereIn('id_kendaraan', $duplicateIds)
                    ->forceDelete();
            });

            $processed++;
            $deleted += count($duplicateIds);
        }

        $deletedForOutput = $dryRun ? 0 : $deleted;
        $singletonForOutput = $dryRun ? 0 : $canonicalizedSingletons;
        $this->info(
            "Selesai. Group diproses: {$processed}, baris dihapus: {$deletedForOutput}, singleton canonicalized: {$singletonForOutput}."
        );

        return self::SUCCESS;
    }
}

