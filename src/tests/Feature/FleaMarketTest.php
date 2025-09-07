<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Enums\ItemCondition;
use App\Models\Category;
use Carbon\Factory;
use Database\Seeders\ItemsTableSeeder;
use Database\Seeders\CategoriesTableSeeder;
use Illuminate\Support\Facades\Hash;


class FleaMarketTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $this->category = Category::create([
            'name' => 'ファッション',
        ]);
    }

    protected function createTestItems(
        int $count = 3,
        ?User $user = null,
        bool $withComments = true,
        bool $withLikes = true
    ) {
        $user ??= $this->user;
        $items = collect();

        for ($i = 0; $i < $count; $i++) {
            $item = Item::create([
                'user_id' => $user->id,
                'title' => '商品名' . $i . ($user->id === $this->user->id ? '' : '_他人の出品'),
                'price' => 1000 * $i,
                'brand' => 'ブランド' . $i,
                'description' => '商品の説明' . $i,
                'image_path' => 'item_images/sample.jpg',
                'condition' => ItemCondition::GOOD,
                'is_sold' => false,
            ]);

            $item->categories()->attach($this->category->id);

            if ($withComments) {
                $item->commentingUsers()->attach($this->user->id, ['text' => 'コメント' . $i]);
            }
            if ($withLikes) {
                $item->likes()->attach($this->user->id);
            }

            $items->push($item);
        }
            return $items;
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */

// 会員登録画面
    public function test_register_userName_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください。',
        ]);
    }


    public function test_register_userEmail_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください。',
        ]);
    }


    public function test_register_userPassword_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください。',
        ]);
    }


    public function test_register_userPassword_too_short_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください。',
        ]);
    }


    public function test_register_userPasswordConfirmation_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '1234567',
            'password_confirmation' => '1234568',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません。',
        ]);
    }


    public function test_register_user_success_redirects_to_profilePage()
    {
        app()->setLocale('ja');

        $email = $this->faker->unique()->safeEmail();

        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'testName',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users',[
            'name'=>'testName',
            'email'=> $email,
        ]);

        $user = User::where('email', $email)->first();
        $this->assertTrue(Hash::check('password', $user->password));

        $response->assertRedirect(route('setProfile'));
    }


// ログイン機能
    public function test_login_userEmail_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください。',
        ]);
    }


    public function test_login_userPassword_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください。',
        ]);
    }


    public function test_login_userEmail_wrong_validationMessage()
    {
        app()->setLocale('ja');

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'notexit' . time() . '@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません。',
        ]);
    }


    public function test_login_userPassword_wrong_validationMessage()
    {
        app()->setLocale('ja');

        $user = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません。',
        ]);
    }


    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($user);
    }


// ログアウト機能
    public function test_logout_success()
    {
        $this->user = User::factory()->create();

        $this->actingAs($this->user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }


// 商品一覧取得
    public function test_get_items_success()
    {
        $items = $this->createTestItems(3, null, false, false);

        $response = $this->get('/');
        $response->assertStatus(200);

        $viewItems = $response->viewData('recommendedItems');

        $this->assertEquals(
            $items->pluck('id')->sort()->values()->toArray(),
            $viewItems->pluck('id')->sort()->values()->toArray()
        );
    }


    public function test_get_items_soldOut_label()
    {
        $items = Item::create([
            'user_id' => $this->user->id,
            'title' => '腕時計',
            'price' => '15000',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ時計',
            'image_path' => 'item_images/Armani_Mens_Clock.jpg',
            'condition' => ItemCondition::GOOD,
            'is_sold' => true,
        ]);

        $items->categories()->attach($this->category->id);

        $response = $this->get('/?tab=recommended');
        $response->assertStatus(200);

        $response->assertSee('sold out');
    }


    public function test_get_items_authUser_view()
    {
        $this->actingAs($this->user);

        $mylistItems = $this->createTestItems(2, $this->user, false, false);

        $otherUser = User::factory()->create();
        $otherItems = $this->createTestItems(3, $otherUser);

        $response = $this->get('/?tab=recommended');
        $response->assertStatus(200);

        $viewItems = $response->viewData('recommendedItems');

        $this->assertEmpty(
            $viewItems->whereIn('id', $mylistItems->pluck('id'))->toArray()
        );
        foreach ($otherItems as $item) {
            $this->assertTrue($viewItems->contains('id', $item->id));
        }
    }


// マイリスト一覧取得
    public function test_get_mylist_items_success()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $items = $this->createTestItems(3, $otherUser, false, false);

        $items->each(function ($item) {
            $item->likes()->attach($this->user->id);
        });

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        $viewItems = $response->viewData('mylistItems');

        $this->assertEquals(
            $items->pluck('id')->sort()->values()->toArray(),
            $viewItems->pluck('id')->sort()->values()->toArray()
        );
    }


    public function test_get_mylist_items_soldOut_label()
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $items = $this->createTestItems(3, $otherUser, false, false);

        $items->each(function ($item) {
            $item->likes()->attach($this->user->id);
        });

        $items[0]->update(['is_sold' => true]);
        $items[1]->update(['is_sold' => true]);
        $items[2]->update(['is_sold' => false]);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        $this->assertStringContainsString('sold out', $response->getContent());

        $this->assertSame(
            2, substr_count($response->getContent(), 'sold out')
        );
    }

    public function test_guest_cannot_see_mylist_items()
    {
        $otherUser = User::factory()->create();
        $items = $this->createTestItems(3, $otherUser, false, false);

        $items[0]->update(['is_sold' => true]);
        $items[1]->update(['is_sold' => true]);
        $items[2]->update(['is_sold' => false]);


        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        $response->assertDontSee($items[0]->title);
        $response->assertDontSee($items[1]->title);
        $response->assertDontSee($items[2]->title);
    }


// 商品検索機能
    public function test_search_items_by_partial_name()
    {
        $items = $this->createTestItems(3, null, false, false);

        $items[0]->update(['title' => 'HDD']);
        $items[1]->update(['title' => '玉ねぎ３束']);
        $items[2]->update(['title' => '腕時計']);

        $response = $this->get(route('items.index', ['keyword' => '時計']));
        $response->assertStatus(200);

        $response->assertDontSee($items[0]->title);
        $response->assertDontSee($items[1]->title);
        $response->assertSee($items[2]->title);
    }


    public function test_search_keyword_is_preserved_in_mylist_tab()
    {
    $this->actingAs($this->user);
    
    $otherUser = User::factory()->create();

    $mylistItem = $this->createTestItems(1, $otherUser, false, false)->first();
    $mylistItem->update(['title' => '腕時計']);
    $mylistItem->likes()->attach($this->user->id);

    $otherItem = $this->createTestItems(1, $otherUser, false, false)->first();
    $otherItem->update(['title' => 'スニーカー']);

    $response = $this->get(route('items.index', [
        'keyword' => '時計',
        'tab' => 'recommended',
    ]));

    $response->assertStatus(200);
    $response->assertSee('value="時計"', false);

    $response->assertSee($mylistItem->title);
    $response->assertDontSee($otherItem->title);

    $response = $this->get(route('items.index', [
        'keyword' => '時計',
        'tab' => 'mylist',
    ]));

    $response->assertStatus(200);
    $response->assertSee('value="時計"', false);

    $response->assertSee($mylistItem->title);
    $response->assertDontSee($otherItem->title);
    }


// 商品詳細情報取得
    public function test_item_detail_page_displays_item_info()
    {
        $otherUser = User::factory()->create();
        $item = $this->createTestItems(1, $otherUser, true, true)->first();

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        $response->assertSee($item->title);
        $response->assertSee($item->price);
        $response->assertSee($item->brand);
        $response->assertSee($item->description);
        $response->assertSee(ItemCondition::label($item->condition));

        $response->assertSee($this->category->name);
        $response->assertSee($item->likes()->count());
        $response->assertSee($item->commentingUsers()->count());

        $item->commentingUsers->each(function ($user) use ($response) {
        $pivotText = $user->pivot->text;
        $response->assertSee($user->name);
        $response->assertSee($pivotText);
        });
    }

    public function test_item_detail_page_displays_multiple_categoryies()
    {
        $otherUser = User::factory()->create();

        $category1 = Category::create(['name' => '子供']);
        $category2 = Category::create(['name' => '家電']);
        $category3 = Category::create(['name' => 'スポーツ']);

        $item = $this->createTestItems(1, $otherUser, true, true)->first();

        $item->categories()->attach([$category1->id, $category2->id, $category3->id]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
        $response->assertSee($category3->name);
    }


    // いいね機能
    public function test_user_can_like_item_and_like_count_increases()
    {
        $this->actingAs($this->user);
        $otherUser = User::factory()->create();

        $item = $this->createTestItems(1, $otherUser, false, false)->first();

        $beforeCount = $item->likes()->count();
        $this->assertEquals(0, $beforeCount);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response = $this->post(route('item.like', $item));

        $item->refresh();

        $afterCount = $item->likes()->count();

        $this->assertEquals($beforeCount + 1, $afterCount);

        $response = $this->get("/item/{$item->id}");
        $response->assertSee((string)$afterCount);
    }

    public function test_like_icom_change_color()
    {
        $this->actingAs($this->user);
        $otherUser = User::factory()->create();

        $item = $this->createTestItems(1, $otherUser, false, false)->first();

        $item->likes()->attach($this->user->id);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee('class="button-like button-unlike"', false);
    }

    public function test_user_can_unlike_item_and_count_decreases()
    {
        $this->actingAs($this->user);
        $otherUser = User::factory()->create();

        $item = $this->createTestItems(1, $otherUser, false, false)->first();
        
        $this->user->mylistItems()->attach($item->id);

        $this->assertDatabaseHas('likes', [
        'user_id' => $this->user->id,
        'item_id' => $item->id,
        ]);
        $beforeCount = $item->likes()->count();

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response = $this->post("/item/{$item->id}/like");
        $response->assertRedirect("/item/{$item->id}");

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
        ]);

        $item->refresh();
        $afterCount = $item->likes()->count();

        $this->assertEquals($beforeCount - 1, $afterCount);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee((string)$afterCount);
    }

    // コメント送信機能
    public function test_logged_in_user_can_post_comment()
    {
        $this->actingAs($this->user);

        $item = $this->createTestItems(1, $this->user, false, false)->first();

        $response = $this->post(route('item.comment', $item->id), [
            'text' => 'テストコメント',
        ]);

        $response->assertRedirect("/item/{$item->id}");

        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'text'    => 'テストコメント',
        ]);
    }

    public function test_guest_cannot_post_comment()
    {
        $item = $this->createTestItems(1, $this->user, false, false)->first();

        $response = $this->post(route('item.comment', $item->id), [
            'text' => 'ゲストのコメント',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'text'    => 'ゲストのコメント',
        ]);
    }

    public function test_comment_validation_error_when_empty()
    {
        $this->actingAs($this->user);

        $item = $this->createTestItems(1, $this->user, false, false)->first();

        $response = $this->from(route('item.detail', $item->id))
                        ->post(route('item.comment', $item->id), [
                            'text' => '',
                        ]);

        $response->assertRedirect(route('item.detail', $item->id));

        $response->assertSessionHasErrors([
            'text' => 'コメントを入力してください。',
        ]);
    }


    public function test_comment_validation_error_when_too_long()
    {
        $this->actingAs($this->user);

        $item = $this->createTestItems(1, $this->user, false, false)->first();

        $longText = str_repeat('あ', 256);

        $response = $this->from(route('item.detail', $item->id))
                        ->post(route('item.comment', $item->id), [
                            'text' => $longText,
                        ]);

        $response->assertRedirect(route('item.detail', $item->id));

        $response->assertSessionHasErrors([
            'text' => 'コメントは255文字以内で入力してください',
        ]);
    }


}