<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Kapal;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        User::updateOrCreate(
            ['email' => 'admin@pms.co.id'],
            [
                'name'     => 'Administrator Logistik',
                'password' => Hash::make('password'),
            ]
        );

        $jsonPath = base_path('../full_data.json');
        if (!File::exists($jsonPath)) {
            $jsonPath = base_path('full_data.json');
        }

        if (File::exists($jsonPath)) {
            $data = json_decode(File::get($jsonPath), true);

            // 2. Categories
            $catMap = [];
            $categories = [
                'SUKU CADANG',
                'SUPORTING SUPPLIES',
                'ELECTRIC',
                'Dokumen',
                'ALAT TULIS KANTOR (ATK)',
            ];
            foreach ($categories as $cName) {
                $k = Kategori::firstOrCreate(['nama' => $cName]);
                $catMap[$cName] = $k->id;
            }

            // 3. Ships
            $shipMap = [];
            $shipList = [
                'H.101', 'H.102', 'H.103', 'H.106', 'H.108', 'H.111',
                'H.115', 'H.116', 'H.117', 'H.777', 'H.888',
                'KM.CATLEYA', 'KM.Q S', 'KM. PANTOK', 'BG. DLL'
            ];
            foreach ($shipList as $sCode) {
                $tipe = str_contains($sCode, 'BG') ? 'tongkang' : 'kapal';
                $k = Kapal::firstOrCreate(
                    ['kode' => $sCode],
                    ['nama' => $sCode, 'tipe' => $tipe, 'aktif' => true]
                );
                $shipMap[$sCode] = $k->id;
            }

            // 4. Barangs
            $itemsData = $data['items'] ?? [];
            $barangMap = [];
            $count = 1;

            foreach ($itemsData as $it) {
                $kode = 'BRG-' . str_pad($count++, 5, '0', STR_PAD_LEFT);
                $catName = trim($it['kategori'] ?? 'SUPORTING SUPPLIES');
                $catId = $catMap[$catName] ?? $catMap['SUPORTING SUPPLIES'];

                $stokAwal = (int)($it['awal'] ?? 0);
                $masuk    = (int)($it['masuk'] ?? 0);
                $keluar   = (int)($it['keluar'] ?? 0);
                $sisa     = (int)($it['sisa'] ?? 0);
                $harga    = (float)($it['harga'] ?? 0);

                // If stok_awal is 0 but there is sisa/keluar, calculate stok_awal
                if ($stokAwal === 0 && ($sisa > 0 || $keluar > 0)) {
                    $stokAwal = $sisa + $keluar - $masuk;
                }

                $b = Barang::create([
                    'kode_barang'   => $kode,
                    'nama'          => trim($it['nama'] ?? ('Item ' . $count)),
                    'satuan'        => trim($it['satuan'] ?? 'Pcs'),
                    'kategori_id'   => $catId,
                    'harga_satuan'  => $harga,
                    'stok_awal'     => max(0, $stokAwal),
                    'stok_sekarang' => max(0, $sisa),
                ]);

                // Map by exact name and normalized lowercase name
                $barangMap[trim($it['nama'])] = $b;
                $barangMap[strtolower(trim($it['nama']))] = $b;
            }

            // 5. Initial Transactions from Ship Outgoing Data
            $shipData = $data['ship_data'] ?? [];
            $trxCount = 1;

            foreach ($shipData as $shipCode => $sInfo) {
                $items = $sInfo['items'] ?? [];
                if (empty($items)) continue;

                $trx = Transaksi::create([
                    'no_transaksi' => 'KLR-202109' . str_pad($trxCount++, 4, '0', STR_PAD_LEFT),
                    'tanggal'      => '2021-09-30',
                    'tipe'         => 'keluar',
                    'kapal_id'     => $shipMap[$shipCode] ?? null,
                    'keterangan'   => 'Pengeluaran barang September 2021 - Armada ' . $shipCode,
                    'dibuat_oleh'  => 'System Seeder',
                ]);

                foreach ($items as $itemOut) {
                    $bName = trim($itemOut['nama'] ?? '');
                    $b = $barangMap[$bName] ?? ($barangMap[strtolower($bName)] ?? null);

                    // Fuzzy search if direct match fails
                    if (!$b && $bName) {
                        foreach ($barangMap as $key => $targetB) {
                            if (is_string($key) && (str_contains(strtolower($key), strtolower($bName)) || str_contains(strtolower($bName), strtolower($key)))) {
                                $b = $targetB;
                                break;
                            }
                        }
                    }

                    if ($b) {
                        $qty   = (int)($itemOut['qty'] ?? 1);
                        $val   = (float)($itemOut['nilai'] ?? 0);
                        $harga = ($val > 0 && $qty > 0) ? ($val / $qty) : $b->harga_satuan;

                        TransaksiItem::create([
                            'transaksi_id' => $trx->id,
                            'barang_id'    => $b->id,
                            'jumlah'       => $qty,
                            'harga_satuan' => $harga,
                            'subtotal'     => $val > 0 ? $val : ($harga * $qty),
                        ]);
                    }
                }
            }
        }
    }
}
