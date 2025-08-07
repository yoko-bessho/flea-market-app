<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Item;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = Item::all();
        $users = User::all();

        if ($items->isEmpty() || $users->isEmpty()) {
            return;
        }

        $item1 = $items->first();
        $commenter1 = $users->where('id', '!=', $item1->user_id)->first();

        if ($item1 && $commenter1) {
            Comment::create([
                'user_id' => $commenter1->id,
                'item_id' => $item1->id,
                'text' => '購入を検討しています。値下げは可能でしょうか？'
            ]);
        }

        $item2 = $items->skip(1)->first();
        if ($item2) {
            $commenter2 = $users->where('id', '!=', $item2->user_id)->first();
            if ($commenter2) {
                Comment::create([
                    'user_id' => $commenter2->id,
                    'item_id' => $item2->id,
                    'text' => '商品の状態について詳しく教えていただけますか？'
                ]);
            }
        }
    }
}
