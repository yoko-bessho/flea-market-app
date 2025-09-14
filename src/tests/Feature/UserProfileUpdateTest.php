<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_profile_edit_page_displays_initial_values_and_can_be_updated()
    {
        $user = User::factory()->create([
            'name' => 'Initial Name',
            'postal_code' => '123-4567',
            'address' => 'Initial Address',
            'building' => 'Initial Building',
            'profile_image' => null,
        ]);

        $response = $this->actingAs($user)->get(route('setProfile'));

        $response->assertStatus(200);
        $response->assertSee('Initial Name');
        $response->assertSee('123-4567');
        $response->assertSee('Initial Address');
        $response->assertSee('Initial Building');

        Storage::fake('public');
        $new_image = UploadedFile::fake()->image('avatar.jpg');

        $updateData = [
            'name' => 'Updated Name',
            'postal_code' => '765-4321',
            'address' => 'Updated Address',
            'building' => 'Updated Building',
            'profile_image' => $new_image,
        ];

        $response = $this->post(route('update'), $updateData);

        $response->assertRedirect('/');

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('765-4321', $user->postal_code);
        $this->assertEquals('Updated Address', $user->address);
        $this->assertEquals('Updated Building', $user->building);
        $this->assertNotNull($user->profile_image);
        Storage::disk('public')->assertExists($user->profile_image);
    }
}
