<?php

namespace Tests;

class AuthenticationTest extends TestCase
{
    public function test_login_admin()
    {
        $response = $this->call('POST', '/api/v1/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'admin',
        ])->assertJsonStructure([
            'data' => [
                'token',
                'refresh_token',
            ],
        ]);

        $this->assertEquals(200, $response->status());
    }
}
