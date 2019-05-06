<?php

namespace Junges\ACL\Test;

use Junges\ACL\Test\TestCase;

class UserHasPermissionTest extends TestCase
{
    /**
     * Setup
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function can_give_permission_to_user()
    {
        $this->assertIsObject($this->testUser->assignPermissions([$this->testUserPermission]));
    }

    /**
     * @test
     */
    public function can_revoke_permissions_of_user()
    {
        $this->assertIsObject($this->testUser->revokePermissions([$this->testUserPermission]));
    }
}
