<?php

namespace Database\Seeders;

use App\Enums\ItemCondition;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::where('email', 'test@example.com')->first();

        $categoryMap = Category::pluck('id','name');

        $items = [
            [
                'user_id' => $user->id,
                'title' => '腕時計',
                'price' => '15000',
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ時計',
                'image_path' => 'item_images/Armani_Mens_Clock.jpg',
                'condition' => ItemCondition::GOOD,
                'categories' => ['ファッション', 'メンズ'],
                'is_sold' => true,
            ],
            [
                'user_id' => $user->id,
                'title' => 'HDD',
                'price' => '5000',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'item_images/HDD_Hard_Disk.jpg',
                'condition' => ItemCondition::OK,
                'categories' => ['家電'],
                'is_sold' => true,
            ],
            [
                'user_id' => $user->id,
                'title' => '玉ねぎ3束',
                'price' => '300',
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'item_images/onion.jpg',
                'condition' => ItemCondition::FAIR,
                'categories' => ['食品'],
                'is_sold' => false,
            ],
            [
                'user_id' => $user->id,
                'title' => '革靴',
                'price' => '4000',
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'item_images/Leather_Shoes.jpg',
                'condition' => ItemCondition::POOR,
                'categories' => ['ファッション', 'メンズ'],
                'is_sold' => false,
            ],
            [
                'user_id' => $user->id,
                'title' => 'ノートPC',
                'price' => '45000',
                'brand' => '',
                'description' => '高性能なノートバソコン',
                'image_path' => 'item_images/Living_Room_Laptop.jpg',
                'condition' => ItemCondition::GOOD,
                'categories' => ['家電'],
                'is_sold' => false,
            ],
            [
                'user_id' => 2,
                'title' => 'マイク',
                'price' => '8000',
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'item_images/Music_Mic.jpg',
                'condition' => ItemCondition::OK,
                'categories' => ['家電'],
                'is_sold' => true,
            ],
            [
                'user_id' => 2,
                'title' => 'ショルダーバッグ',
                'price' => '3500',
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'item_images/Purse_fashion_pocket.jpg',
                'condition' => ItemCondition::FAIR,
                'categories' => ['ファッション', 'レディース'],
                'is_sold' => true,
            ],
            [
                'user_id' => 2,
                'title' => 'タンブラー',
                'price' => '500',
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'image_path' => 'item_images/Tumbler_souvenir.jpg',
                'condition' => ItemCondition::POOR,
                'categories' => ['キッチン'],
                'is_sold' => true,
            ],
            [
                'user_id' => 2,
                'title' => 'コーヒーミル',
                'price' => '4000',
                'brand' => 'starbacks',
                'description' => '手動のコーヒーミル',
                'image_path' => 'item_images/Waitress_with_Coffee_Grinder.jpg',
                'condition' => ItemCondition::GOOD,
                'categories' => ['キッチン'],
                'is_sold' => false,
            ],
            [
                'user_id' => 2,
                'title' => 'メイクセット',
                'price' => '2500',
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'image_path' => 'item_images/makeup_set.jpg',
                'condition' => ItemCondition::OK,
                'categories' => ['コスメ', 'レディース'],
                'is_sold' => false,
            ],
        ];
        foreach ($items as $itemData) {
            $item = Item::create([
                'user_id' => $itemData['user_id'],
                'title' => $itemData['title'],
                'price' => $itemData['price'],
                'brand' => $itemData['brand'],
                'description' => $itemData['description'],
                'image_path' => $itemData['image_path'],
                'condition' => $itemData['condition'],
                'is_sold' => $itemData['is_sold'],
            ]);

            $categoryIds = collect($itemData['categories'])
                ->map(fn($name) => $categoryMap[$name])
                ->all();

            $item->categories()->attach($categoryIds);
        }
    }
}
