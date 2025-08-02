<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;

class LikesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $items = Item::all();

        foreach ($users as $user) {
            $otherItems = $items->where('user_id', '!=', $user->id)->pluck('id')->all();
        
            if (empty($otherItems)) {
                continue;
            }

            $likeCount = rand(3,5);
            $randomItemIds = collect($otherItems)->random($likeCount);

            $user->mylistItems()->syncWithoutDetaching($randomItemIds);
        }

    }
}
