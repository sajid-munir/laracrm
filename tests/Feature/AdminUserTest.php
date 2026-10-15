<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin, 'admin');
    }

    public function test_users_page_renders(): void
    {
        $this->get(route('users.index'))->assertOk()->assertSee('All Users');
    }

    public function test_list_marks_the_signed_in_admin_and_filters_by_name(): void
    {
        User::factory()->create(['name' => 'Sara Ahmed']);

        $this->getJson(route('users.data'))
            ->assertOk()
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonFragment(['id' => $this->admin->id, 'is_self' => true]);

        $this->getJson(route('users.data', ['name' => 'sara']))
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonPath('users.0.name', 'Sara Ahmed');
    }

    public function test_admin_can_add_a_user_with_a_hashed_password(): void
    {
        $this->postJson(route('users.store'), [
            'name' => 'New Admin',
            'email' => 'new@example.com',
            'password' => 'secret1',
            'status' => 'Active',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('secret1', User::query()->where('email', 'new@example.com')->firstOrFail()->password));
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson(route('users.store'), [
            'name' => 'Someone',
            'email' => 'taken@example.com',
            'password' => 'secret1',
            'status' => 'Active',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email' => 'A user with that email address already exists.']);
    }

    public function test_admin_cannot_deactivate_or_delete_themselves(): void
    {
        $this->putJson(route('users.update', $this->admin), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'status' => 'Inactive',
        ])->assertUnprocessable()->assertJsonValidationErrors(['status' => 'You cannot deactivate your own account.']);

        $this->deleteJson(route('users.destroy', $this->admin))
            ->assertUnprocessable()
            ->assertJson(['message' => 'You cannot delete your own account.']);
    }

    public function test_admin_can_update_and_delete_another_user(): void
    {
        $other = User::factory()->create();

        $this->putJson(route('users.update', $other), [
            'name' => 'Renamed',
            'email' => $other->email,
            'status' => 'Inactive',
        ])->assertOk();

        $this->assertSame('Renamed', $other->fresh()->name);
        $this->assertSame('Inactive', $other->fresh()->status);

        $this->deleteJson(route('users.destroy', $other))->assertOk();
        $this->assertModelMissing($other);
    }
}
