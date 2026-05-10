<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Chat;
use App\Models\ChatbotResponse;
use App\Models\Conversation;
use App\Models\Courier;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Report;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
<<<<<<< HEAD
        $this->call(AdminUserSeeder::class);
        $this->call(ProductSeeder::class);
    }
}

=======
        $this->call([
            AdminUserSeeder::class,
            CourierSeeder::class,
            ChatbotResponseSeeder::class,
            ArticleSeeder::class,
            SellerStoreProductSeeder::class,
            BuyerOrderChatReportSeeder::class,
            ReturnComprehensiveSeeder::class,
        ]);
    }
}
>>>>>>> 4618886ce8ec2779a7c8dff7bb6f1acbff2c0b54
