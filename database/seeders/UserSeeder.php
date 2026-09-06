<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users with supporting profile data.
     */
    public function run(): void
    {
        /** @var User $admin */
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@mukhamar.sa'],
            [
                'name' => 'مدير المتجر',
                'phone' => '+966 11 234 5678',
                'password' => bcrypt('password'),
                'avatar' => null,
                'lang' => 'ar',
                'is_active' => true,
            ]
        );
        $admin->markEmailAsVerified();

        /** @var User $noura */
        $noura = User::query()->updateOrCreate(
            ['email' => 'n.alabdullah@example.com'],
            [
                'name' => 'نورة العبدالله',
                'phone' => '+966 50 123 4567',
                'password' => bcrypt('password'),
                'avatar' => null,
                'lang' => 'ar',
                'is_active' => true,
            ]
        );
        $noura->markEmailAsVerified();

        // Demo customer seen in the checkout mockup.
        $this->seedDemoCustomer();

        // A handful of extra customers for a richer demo.
        $extraNames = [
            'سارة أحمد',
            'محمد العتيبي',
            'خالد القحطاني',
            'ريم العنزي',
            'فهد الشمري',
            'لولوة السبيعي',
            'عبدالله الدوسري',
        ];

        foreach ($extraNames as $name) {
            $user = User::factory()->create(['name' => $name]);
            $user->markEmailAsVerified();

            $this->seedAddresses($user);
        }
    }

    /**
     * Seed the specific demo customer shown throughout the mockups.
     */
    private function seedDemoCustomer(): void
    {
        /** @var User $customer */
        $customer = User::factory()->create([
            'name' => 'محمد أحمد',
            'email' => 'customer@example.com',
            'phone' => '+966 55 321 6547',
            'lang' => 'ar',
            'is_active' => true,
        ])->first();

        $this->seedAddresses($customer);
    }

    /**
     * Seed default delivery addresses for a user.
     */
    private function seedAddresses(User $user): void
    {
        $count = $user->addresses()->count();

        if ($count === 0) {
            Address::factory()->default()->create([
                'user_id' => $user->id,
                'label' => 'home',
                'recipient_name' => $user->name,
                'street' => 'شارع التحلية',
                'district' => 'حي العليا',
                'building' => 'مبنى 45',
            ]);

            Address::factory()->create([
                'user_id' => $user->id,
                'label' => 'work',
                'recipient_name' => $user->name,
                'street' => 'طريق الملك فهد',
                'district' => 'الرياض',
                'building' => 'برج المملكة',
            ]);
        }
    }
}
