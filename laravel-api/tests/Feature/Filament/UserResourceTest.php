<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Filament\Resources\UserResource as UserFilamentResource;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // Create an admin to bypass the canAccessPanel check
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** --- LIST PAGE TESTS --- **/

    public function test_it_can_render_the_list_users_page(): void
    {
        $this->actingAs($this->admin)
            ->get(UserFilamentResource::getUrl('index'))
            ->assertStatus(200);
    }

    public function test_it_can_list_users(): void
    {
        $users = User::factory()->count(5)->create();

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords($users)
            ->assertCountTableRecords(6); // 5 + the logged-in admin
    }

    /** --- EDIT PAGE TESTS --- **/

    public function test_it_can_render_the_edit_user_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->get(UserFilamentResource::getUrl('edit', ['record' => $user]))
            ->assertStatus(200);
    }

    public function test_it_can_retrieve_user_data_for_editing(): void
    {
        $user = User::factory()->create(['name' => 'Original Name']);

        Livewire::test(EditUser::class, [
            'record' => $user->getRouteKey(),
        ])
            ->assertFormSet([
                'name' => 'Original Name',
                'email' => $user->email,
            ]);
    }

    public function test_it_can_save_user_data(): void
    {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, [
            'record' => $user->getRouteKey(),
        ])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }
}
