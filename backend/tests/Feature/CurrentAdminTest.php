<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CurrentAdminTest extends TestCase
{
    public function test_guests_cannot_access_the_admin_endpoint(): void
    {
        $this->getJson('/api/admin/me')->assertUnauthorized();
    }

    public function test_customers_cannot_access_the_admin_endpoint(): void
    {
        $customer = (new User)->forceFill(['role' => 'customer', 'status' => 'active']);

        $this->actingAs($customer)->getJson('/api/admin/me')->assertForbidden();
    }

    public function test_suspended_admins_cannot_access_the_admin_endpoint(): void
    {
        $admin = (new User)->forceFill(['role' => 'admin', 'status' => 'suspended']);

        $this->actingAs($admin)->getJson('/api/admin/me')->assertForbidden();
    }

    public function test_active_admins_can_read_their_profile(): void
    {
        $admin = (new User)->forceFill([
            'id' => 1,
            'name' => 'Test Admin',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->getJson('/api/admin/me')
            ->assertOk()
            ->assertExactJson(['id' => 1, 'name' => 'Test Admin']);
    }
}
