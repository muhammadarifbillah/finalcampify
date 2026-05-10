<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Conversation;
use App\Models\Courier;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Report;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BuyerOrderChatReportSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = $this->buyerSpecs();
        $products = Product::with('store')
            ->where('status', 'approved')
            ->get();

        if ($products->isEmpty()) {
            $products = Product::with('store')->get();
        }

        if ($products->isEmpty()) {
            // Tidak ada produk untuk dijadikan transaksi
            return;
        }

        $couriers = Courier::where('status', 'aktif')
            ->orderBy('service')
            ->orderBy('name')
            ->get();

        $receiptPath = $this->findAnyReceiptPath();

        foreach ($buyers as $buyerSpec) {
            $buyer = $this->upsertBuyer($buyerSpec);
            $this->seedOrdersAndTransactions($buyer, $products, $couriers, $receiptPath);
        }
    }

    private function buyerSpecs(): array
    {
        return [
            ['email' => 'agus.pratama@gmail.com', 'name' => 'Agus Pratama', 'city' => 'Bandung', 'district' => 'Lengkong'],
            ['email' => 'siti.aminah@yahoo.com', 'name' => 'Siti Aminah', 'city' => 'Jakarta', 'district' => 'Setiabudi'],
            ['email' => 'bambang.h@gmail.com', 'name' => 'Bambang Herlambang', 'city' => 'Bandung', 'district' => 'Coblong'],
            ['email' => 'dewi.lestari@outlook.com', 'name' => 'Dewi Lestari', 'city' => 'Surabaya', 'district' => 'Tegalsari'],
            ['email' => 'eko.pras@gmail.com', 'name' => 'Eko Prasetyo', 'city' => 'Yogyakarta', 'district' => 'Depok'],
            ['email' => 'farida.putri@gmail.com', 'name' => 'Farida Putri', 'city' => 'Semarang', 'district' => 'Banyumanik'],
            ['email' => 'gilang.r@gmail.com', 'name' => 'Gilang Ramadhan', 'city' => 'Malang', 'district' => 'Lowokwaru'],
            ['email' => 'hani.safira@gmail.com', 'name' => 'Hani Safira', 'city' => 'Denpasar', 'district' => 'Denpasar Selatan'],
            ['email' => 'indra.wijaya@gmail.com', 'name' => 'Indra Wijaya', 'city' => 'Makassar', 'district' => 'Panakkukang'],
            ['email' => 'joko.susilo@gmail.com', 'name' => 'Joko Susilo', 'city' => 'Solo', 'district' => 'Laweyan'],
        ];
    }

    private function upsertBuyer(array $spec): User
    {
        $phoneTail = str_pad((string) (crc32($spec['email']) % 100000000), 8, '0', STR_PAD_LEFT);

        return User::updateOrCreate(
            ['email' => $spec['email']],
            [
                'name' => $spec['name'],
                'nama' => $spec['name'],
                // Pola password seragam: (Email local part, capitalize) + "123?"
                'password' => Hash::make($this->passwordForEmail($spec['email'])),
                'role' => 'buyer',
                'status' => 'active',
                'last_login' => now()->subDays((int) (crc32($spec['email']) % 14)),
                'address' => 'Jl. ' . $spec['district'] . ' No. ' . ((crc32($spec['name']) % 90) + 10),
                'city' => $spec['city'],
                'district' => $spec['district'],
                'postal_code' => (string) (10000 + (crc32($spec['city']) % 80000)),
                'phone' => '08' . $phoneTail,
            ]
        );
    }

    private function passwordForEmail(string $email): string
    {
        $local = trim(explode('@', $email)[0] ?? '');
        $local = $local !== '' ? $local : 'buyer';
        return Str::ucfirst($local) . '123?';
    }

    private function seedOrdersAndTransactions(User $buyer, $products, $couriers, ?string $receiptPath): void
    {
        $targetOrders = 3;
        $existingOrders = Order::where('user_id', $buyer->id)->count();

        // Pastikan data rentals terisi untuk order lama (jika ada)
        $this->backfillRentalsForBuyer($buyer);

        if ($existingOrders >= $targetOrders) {
            return;
        }

        $seed = crc32($buyer->email);
        $productsSorted = $products->sortBy(fn(Product $p) => crc32(((string) $p->id) . '|' . $seed))->values();
        $rentalProductsSorted = $productsSorted
            ->filter(fn(Product $p) => (int) ($p->rent_price ?? 0) > 0 || (bool) ($p->is_rental ?? false) || ($p->jenis_produk ?? null) === 'sewa')
            ->values();
        $couriersSorted = $couriers->values();

        for ($i = $existingOrders; $i < $targetOrders; $i++) {
            $orderCreatedAt = now()->subDays((int) (($seed + $i) % 12));
            $itemCount = 1 + (int) (($seed + ($i * 7)) % 3);

            $items = $productsSorted
                ->slice(($i * 3) % max(1, $productsSorted->count()), $itemCount)
                ->values();

            if ($items->isEmpty()) {
                continue;
            }

            // Kalau buyer belum punya data sewa sama sekali, paksa minimal 1 item sewa
            if ($this->buyerNeedsRentalSeed($buyer) && $rentalProductsSorted->isNotEmpty()) {
                $pick = $rentalProductsSorted[($i + $seed) % $rentalProductsSorted->count()];
                if (!$items->contains(fn(Product $p) => $p->id === $pick->id)) {
                    $items = $items->slice(0, max(0, $itemCount - 1))->prepend($pick)->values();
                }
            }

            $courier = $couriersSorted->isNotEmpty()
                ? $couriersSorted[($i + $seed) % $couriersSorted->count()]
                : null;

            $shippingCost = (int) ($courier?->price ?? 0);
            $kurirService = (string) ($courier?->service ?? 'jne');
            $paymentMethod = ['transfer', 'ewallet', 'cod'][($seed + $i) % 3];
            $orderStatus = ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'][($seed + ($i * 3)) % 5];

            $subtotal = 0;

            $order = Order::create([
                'user_id' => $buyer->id,
                'receiver_name' => $buyer->name,
                'total' => 0,
                'shipping_address' => $buyer->address,
                'shipping_city' => $buyer->city,
                'shipping_district' => $buyer->district,
                'shipping_postal_code' => $buyer->postal_code,
                'shipping_phone' => $buyer->phone,
                'metode_pembayaran' => $paymentMethod,
                'status' => $orderStatus,
                'kurir' => $kurirService,
                'no_resi' => $orderStatus === 'dikirim' || $orderStatus === 'selesai'
                    ? strtoupper($kurirService) . '-' . ($buyer->id) . '-' . str_pad((string) (($seed + $i) % 100000), 5, '0', STR_PAD_LEFT)
                    : null,
            ]);

            // Kolom tambahan (tidak ada di $fillable Order)
            if ($receiptPath && in_array($paymentMethod, ['transfer', 'ewallet'], true) && $orderStatus !== 'dibatalkan') {
                $order->bukti_pembayaran = $receiptPath;
            }
            $order->latitude = -6.20000000 + (((int) ($seed % 100)) / 10000);
            $order->longitude = 106.81666667 + (((int) (($seed + 33) % 100)) / 10000);
            $order->created_at = $orderCreatedAt;
            $order->updated_at = $orderCreatedAt->copy()->addHours((int) (($seed + $i) % 36));
            $order->save();

            foreach ($items as $index => $product) {
                $rentPrice = (int) ($product->rent_price ?? 0);
                $buyPrice = (int) ($product->buy_price ?? 0);
                $isRentalProduct = $rentPrice > 0 || (bool) ($product->is_rental ?? false) || ($product->jenis_produk ?? null) === 'sewa';
                $isRental = $isRentalProduct && ($buyPrice <= 0 || (($seed + $index + $i) % 3 === 0));
                $type = $isRental ? 'rent' : 'buy';
                $duration = $isRental ? (1 + (int) (($seed + $index) % 5)) : null;
                $unitPrice = $isRental
                    ? $rentPrice
                    : (int) ($buyPrice > 0 ? $buyPrice : ($product->price ?? 0));
                $qty = $isRental ? 1 : (1 + (int) (($seed + $index) % 3));
                $lineTotal = $unitPrice * $qty * ($isRental ? max(1, (int) $duration) : 1);
                $subtotal += $lineTotal;

                $detail = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'harga' => $unitPrice,
                    'type' => $type,
                    'duration' => $duration,
                    'start_date' => $isRental ? Carbon::parse($orderCreatedAt)->addDays(1) : null,
                ]);

                // Legacy transaksi untuk kebutuhan admin/store stats
                $this->insertLegacyTransaction($buyer->id, $product->id, $lineTotal, $orderCreatedAt);

                // Sinkronkan tabel rentals untuk transaksi sewa
                if ($type === 'rent') {
                    $this->upsertRentalFromOrderDetail($buyer, $order, $product, $detail, $orderCreatedAt, $lineTotal);
                }

                // Sebagian transaksi dibuatkan percakapan + potensi laporan chat
                if ($i === 0 && $index === 0) {
                    $this->seedConversationAndReports($buyer, $product, $order, $detail, $seed);
                }
            }

            $order->total = $subtotal + $shippingCost;
            $order->save();

            // Sebagian transaksi dibuatkan chat yang ditandai sistem
            if (($seed + $i) % 3 === 0) {
                $this->seedFlaggedChat($buyer, $order, $items->first(), $seed + $i);
            }

            // Sebagian buyer melaporkan toko (tidak semua)
            if (($seed + $i) % 4 === 0) {
                $this->seedStoreReport($buyer, $items->first(), $orderCreatedAt, $seed + $i);
            }
        }
    }

    private function buyerNeedsRentalSeed(User $buyer): bool
    {
        if (!DB::getSchemaBuilder()->hasTable('rentals')) {
            return false;
        }

        return !DB::table('rentals')->where('user_id', $buyer->id)->exists();
    }

    private function backfillRentalsForBuyer(User $buyer): void
    {
        if (!DB::getSchemaBuilder()->hasTable('rentals')) {
            return;
        }

        $orders = Order::where('user_id', $buyer->id)->pluck('id')->all();
        if (empty($orders)) {
            return;
        }

        $rentDetails = OrderDetail::whereIn('order_id', $orders)->where('type', 'rent')->get();
        foreach ($rentDetails as $detail) {
            $order = Order::find($detail->order_id);
            $product = Product::find($detail->product_id);
            if (!$order || !$product) {
                continue;
            }

            $lineTotal = (int) ($detail->harga ?? 0) * max(1, (int) ($detail->duration ?? 1));
            $this->upsertRentalFromOrderDetail($buyer, $order, $product, $detail, $order->created_at ?? now(), $lineTotal);
        }
    }

    private function upsertRentalFromOrderDetail(User $buyer, Order $order, Product $product, OrderDetail $detail, $createdAt, int $lineTotal): void
    {
        if (!DB::getSchemaBuilder()->hasTable('rentals')) {
            return;
        }

        $start = $detail->start_date ? Carbon::parse($detail->start_date) : Carbon::parse($createdAt)->addDays(1);
        $duration = max(1, (int) ($detail->duration ?? 1));
        $end = $start->copy()->addDays($duration);

        $status = match ($order->status) {
            'menunggu' => 'pending',
            'diproses' => 'active',
            'dikirim' => 'active',
            'selesai' => 'selesai',
            'dibatalkan' => 'cancelled',
            default => 'pending',
        };

        $key = [
            'user_id' => $buyer->id,
            'order_id' => $order->id,
            'product_id' => $product->id,
        ];

        $payload = [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'duration' => $duration,
            'price' => $lineTotal,
            'status' => $status,
            'updated_at' => $createdAt,
        ];

        if (DB::getSchemaBuilder()->hasColumn('rentals', 'created_at')) {
            $payload['created_at'] = $createdAt;
        }

        DB::table('rentals')->updateOrInsert($key, $payload);
    }

    private function insertLegacyTransaction(int $buyerId, int $productId, int $total, $createdAt): void
    {
        if (!DB::getSchemaBuilder()->hasTable('transactions')) {
            return;
        }

        $payload = [
            'user_id' => $buyerId,
            'product_id' => $productId,
            'total' => $total,
        ];

        if (DB::getSchemaBuilder()->hasColumn('transactions', 'created_at')) {
            $payload['created_at'] = $createdAt;
            $payload['updated_at'] = $createdAt;
        }

        DB::table('transactions')->insert($payload);
    }

    private function seedConversationAndReports(User $buyer, Product $product, Order $order, OrderDetail $detail, int $seed): void
    {
        $sellerId = $product->sellerUserId();
        if (!$sellerId || $sellerId === $buyer->id) {
            return;
        }

        $conversation = Conversation::firstOrCreate([
            'product_id' => $product->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $sellerId,
        ]);

        $baseCreatedAt = $order->created_at ?? now();
        $messages = [
            ['sender_id' => $buyer->id, 'text' => "Halo kak, untuk {$product->name} masih ready?"],
            ['sender_id' => $sellerId, 'text' => 'Masih ready kak, stok aman. Kakak mau beli atau sewa?'],
            ['sender_id' => $buyer->id, 'text' => 'Saya mau order sekarang ya. Bisa kirim hari ini?'],
            ['sender_id' => $sellerId, 'text' => 'Bisa kak, kami proses maksimal 1x24 jam.'],
        ];

        // Sisipkan satu pesan bermasalah untuk contoh report chat (tidak selalu)
        if (($seed % 5) === 0) {
            $messages[] = ['sender_id' => $buyer->id, 'text' => 'Pelayanan kamu jelek! Ini penipuan ya?'];
        } else {
            $messages[] = ['sender_id' => $buyer->id, 'text' => 'Oke kak, terima kasih infonya.'];
        }

        $createdMessages = [];
        foreach ($messages as $idx => $msg) {
            $createdAt = Carbon::parse($baseCreatedAt)->addMinutes(6 * ($idx + 1));
            $created = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $msg['sender_id'],
                'message' => $msg['text'],
                'read_at' => $msg['sender_id'] === $buyer->id ? null : $createdAt->copy()->addMinutes(2),
            ]);
            $created->created_at = $createdAt;
            $created->updated_at = $createdAt;
            $created->save();

            $createdMessages[] = $created;
        }

        $conversation->updated_at = end($createdMessages)->created_at ?? now();
        $conversation->save();

        // Buat report chat berdasarkan pesan bermasalah (jika ada)
        $problem = collect($createdMessages)->first(fn(Message $m) => str_contains(strtolower($m->message), 'penipuan'));
        if ($problem) {
            Report::create([
                'reporter_id' => $buyer->id,
                'seller_id' => $sellerId,
                'store_id' => $product->store_id,
                'product_id' => $product->id,
                'conversation_id' => $conversation->id,
                'message_id' => $problem->id,
                'type' => 'chat',
                'reason' => 'Bahasa tidak pantas',
                'description' => 'Chat mengandung kata-kata kasar / tuduhan.',
                'status' => 'pending',
            ]);
        }
    }

    private function seedFlaggedChat(User $buyer, Order $order, ?Product $product, int $seed): void
    {
        $sellerId = $product?->sellerUserId();

        $flaggedMessages = [
            'Sistem mendeteksi kata terlarang pada chat. Mohon ditinjau.',
            'Chat terindikasi spam / promosi di luar platform.',
            'Terdeteksi pola percakapan mencurigakan (ajakan transaksi di luar aplikasi).',
        ];

        Chat::create([
            'user_id' => $buyer->id,
            'sender' => 'system',
            'sender_id' => null,
            'receiver_id' => $sellerId,
            'order_id' => $order->id,
            'message' => $flaggedMessages[$seed % count($flaggedMessages)],
            'type' => 'system',
            'is_read' => false,
            'is_flagged' => true,
        ]);
    }

    private function seedStoreReport(User $buyer, ?Product $product, $createdAt, int $seed): void
    {
        if (!$product) {
            return;
        }

        $store = $product->store_id ? Store::find($product->store_id) : null;
        $sellerId = $store?->user_id ?? $product->sellerUserId();
        if (!$sellerId) {
            return;
        }

        $reasons = [
            ['reason' => 'Barang tidak sesuai deskripsi', 'desc' => 'Deskripsi produk tidak sesuai kondisi barang yang diterima.'],
            ['reason' => 'Pengiriman terlambat', 'desc' => 'Pesanan datang lebih lama dari estimasi tanpa informasi jelas.'],
            ['reason' => 'Komunikasi buruk', 'desc' => 'Seller sulit dihubungi saat ditanya terkait pesanan.'],
        ];
        $pick = $reasons[$seed % count($reasons)];

        Report::create([
            'reporter_id' => $buyer->id,
            'seller_id' => $sellerId,
            'store_id' => $store?->id,
            'product_id' => $product->id,
            'type' => 'store',
            'reason' => $pick['reason'],
            'description' => $pick['desc'],
            'status' => ($seed % 2 === 0) ? 'pending' : 'reviewed',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    private function findAnyReceiptPath(): ?string
    {
        $candidate = public_path('uploads/pembayaran');
        if (!is_dir($candidate)) {
            return null;
        }

        $files = glob($candidate . DIRECTORY_SEPARATOR . '*.{png,jpg,jpeg,webp}', GLOB_BRACE);
        if (!$files) {
            return null;
        }

        // Simpan relatif ke public/ untuk asset()
        $filename = basename($files[0]);
        return 'uploads/pembayaran/' . $filename;
    }
}
