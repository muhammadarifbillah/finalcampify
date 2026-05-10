<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If the new schema already exists, finalize any missing constraints / data migration.
        if (Schema::hasTable('returns') && Schema::hasColumns('returns', [
            'order_id',
            'type',
            'status',
            'escrow_total',
            'to_seller',
            'to_buyer',
        ])) {
            $this->ensureRentalForeignKey();
            $this->ensureRentalUniqueIndex();
            $this->migrateLegacyReturnsIfAny();
            return;
        }

        $legacyTable = null;
        if (Schema::hasTable('returns')) {
            $legacyTable = 'returns_legacy';
            if (Schema::hasTable($legacyTable)) {
                $legacyTable = 'returns_legacy_' . date('YmdHis');
            }

            Schema::rename('returns', $legacyTable);
        }

        Schema::create('returns', function (Blueprint $table) {
            $table->id();

            // Core: one record tied to an order (buy/sell or rental).
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Optional: keep rental link for backward compatibility / easier joins.
            // NOTE: we define the FK with an explicit name to avoid collisions when legacy tables
            // are renamed (MySQL requires FK constraint names to be unique within the schema).
            $table->foreignId('rental_id')->nullable();
            $table->foreign('rental_id', 'returns_rental_id_fk_v2')
                ->references('id')
                ->on('rentals')
                ->nullOnDelete();

            $table->enum('type', ['jual_beli', 'sewa'])->default('jual_beli')->index();
            $table->enum('status', ['pending', 'dispute', 'checking', 'completed', 'rejected'])->default('pending')->index();

            $table->decimal('escrow_total', 15, 2)->default(0);
            $table->dateTime('expected_date')->nullable();
            $table->dateTime('actual_date')->nullable();
            $table->decimal('late_fee', 15, 2)->default(0);
            $table->decimal('damage_fee', 15, 2)->default(0);
            $table->decimal('to_seller', 15, 2)->default(0);
            $table->decimal('to_buyer', 15, 2)->default(0);

            // Legacy / operational fields (optional per type).
            $table->string('resi_return')->nullable();
            $table->string('bukti_denda')->nullable();
            $table->string('kondisi_barang')->nullable();
            $table->unsignedBigInteger('denda')->default(0);
            $table->timestamp('tanggal_pengembalian')->nullable();

            $table->timestamps();

            // Keep previous invariant: 1 return record per rental.
            $table->unique('rental_id');
        });

        // Best-effort migration of old data (if we renamed from legacy).
        if (!empty($legacyTable)) {
            $this->migrateLegacyReturnsTable($legacyTable);
        }
    }

    public function down(): void
    {
        // Rollback strategy:
        // - Drop the new table.
        // - If a legacy table exists, restore it back to `returns`.
        if (!Schema::hasTable('returns')) {
            return;
        }

        Schema::drop('returns');

        if (Schema::hasTable('returns_legacy') && !Schema::hasTable('returns')) {
            Schema::rename('returns_legacy', 'returns');
        }
    }

    private function ensureRentalForeignKey(): void
    {
        if (!Schema::hasTable('returns') || !Schema::hasColumn('returns', 'rental_id')) {
            return;
        }

        $hasRentalFk = collect(Schema::getForeignKeys('returns'))
            ->contains(fn ($fk) => in_array('rental_id', $fk['columns'] ?? [], true));

        if ($hasRentalFk) {
            return;
        }

        try {
            Schema::table('returns', function (Blueprint $table) {
                $table->foreign('rental_id', 'returns_rental_id_fk_v2')
                    ->references('id')
                    ->on('rentals')
                    ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // Ignore: the FK may already exist with a different name or be blocked by legacy schema state.
        }
    }

    private function ensureRentalUniqueIndex(): void
    {
        if (!Schema::hasTable('returns') || !Schema::hasColumn('returns', 'rental_id')) {
            return;
        }

        if (Schema::hasIndex('returns', ['rental_id'], 'unique')) {
            return;
        }

        try {
            Schema::table('returns', function (Blueprint $table) {
                $table->unique('rental_id');
            });
        } catch (\Throwable $e) {
            // Ignore if it already exists / can't be created.
        }
    }

    private function migrateLegacyReturnsIfAny(): void
    {
        $tables = Schema::getTableListing(schemaQualified: false);

        foreach ($tables as $table) {
            if (!is_string($table) || !str_starts_with($table, 'returns_legacy')) {
                continue;
            }

            $this->migrateLegacyReturnsTable($table);
        }
    }

    private function migrateLegacyReturnsTable(string $legacyTable): void
    {
        if (!Schema::hasTable('returns') || !Schema::hasTable($legacyTable) || !Schema::hasColumn($legacyTable, 'rental_id')) {
            return;
        }

        DB::table($legacyTable . ' as r')
            ->leftJoin('rentals as rent', 'rent.id', '=', 'r.rental_id')
            ->leftJoin('orders as o', 'o.id', '=', 'rent.order_id')
            ->select([
                'r.id',
                'r.rental_id',
                'r.resi_return',
                'r.bukti_denda',
                'r.kondisi_barang',
                'r.denda',
                'r.tanggal_pengembalian',
                'r.created_at',
                'r.updated_at',
                'rent.order_id as order_id',
                'rent.end_date as expected_end_date',
                'o.total as order_total',
            ])
            ->orderBy('r.id')
            ->chunk(200, function ($rows) {
                $now = now();

                $inserts = [];
                foreach ($rows as $row) {
                    if (empty($row->order_id) || empty($row->rental_id)) {
                        continue;
                    }

                    $actualDate = $row->tanggal_pengembalian ?: $row->created_at;
                    $expectedDate = $row->expected_end_date ? ($row->expected_end_date . ' 00:00:00') : null;

                    $inserts[] = [
                        'order_id' => $row->order_id,
                        'rental_id' => $row->rental_id,
                        'type' => 'sewa',
                        'status' => 'checking',
                        'escrow_total' => (string) ((int) ($row->order_total ?? 0)),
                        'expected_date' => $expectedDate,
                        'actual_date' => $actualDate,
                        'late_fee' => (string) ((int) ($row->denda ?? 0)),
                        'damage_fee' => '0',
                        'to_seller' => '0',
                        'to_buyer' => '0',
                        'resi_return' => $row->resi_return,
                        'bukti_denda' => $row->bukti_denda,
                        'kondisi_barang' => $row->kondisi_barang,
                        'denda' => (int) ($row->denda ?? 0),
                        'tanggal_pengembalian' => $row->tanggal_pengembalian,
                        'created_at' => $row->created_at ?: $now,
                        'updated_at' => $row->updated_at ?: $now,
                    ];
                }

                if (!empty($inserts)) {
                    DB::table('returns')->insertOrIgnore($inserts);
                }
            });
    }
};
