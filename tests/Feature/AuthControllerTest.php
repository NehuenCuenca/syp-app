<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login with valid credentials.
     */
    public function test_login_succeeds_with_valid_credentials(): void
    {
        // Arrange: creamos un usuario que respete el esquema de la tabla users
        $user = User::create([
            'username' => 'john_doe',
            'email'    => 'john@example.com',
            'password' => Hash::make('secret-password'),
            'phone'    => '+54 11 1234 5678',
            'role'     => 'admin', // según enum users_role del diagrama
        ]);

        $payload = [
            'email'    => 'john@example.com',
            'password' => 'secret-password',
        ];

        // Act
        $response = $this->postJson('/api/login', $payload);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login exitoso.', // mensaje definido en AuthController
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'username',
                        'email',
                        'phone',
                        'role',
                    ],
                    'token',
                ],
                'meta',
                'errors',
            ]);

        // Verificamos que se haya creado un token en personal_access_tokens
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id'   => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    /**
     * Test login fails with invalid password.
     */
    public function test_login_fails_with_invalid_password(): void
    {
        // Arrange
        $user = User::create([
            'username' => 'john_doe',
            'email'    => 'john@example.com',
            'password' => Hash::make('correct-password'),
            'phone'    => '+54 11 1234 5678',
            'role'     => 'admin',
        ]);

        $payload = [
            'email'    => 'john@example.com',
            'password' => 'wrong-password',
        ];

        // Act
        $response = $this->postJson('/api/login', $payload);

        // Assert
        $response
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Error de autenticación',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'error_code',
                    'timestamp',
                ],
                'errors' => [
                    'email',
                ],
            ]);

        // Mensaje de error definido en ValidationException::withMessages(...)
        $this->assertSame(
            'Las credenciales proporcionadas son incorrectas.',
            $response->json('errors.email.0')
        );

        // No se debe haber creado ningún token
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id'   => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    /**
     * Test login fails when user does not exist.
     */
    public function test_login_fails_when_user_does_not_exist(): void
    {
        // Arrange: no creamos usuario con ese email
        $payload = [
            'email'    => 'nonexistent@example.com',
            'password' => 'any-password',
        ];

        // Act
        $response = $this->postJson('/api/login', $payload);

        // Assert
        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Los datos proporcionados no son válidos',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'error_code',
                    'timestamp',
                ],
                'errors' => [
                    'email',
                ],
            ]);

        $this->assertSame(
            "El 'correo electronico' especificado no existe.",
            $response->json('errors.email.0')
        );
    }

    /**
     * Test successful logout revokes the current token.
     */
    public function test_logout_succeeds_and_revokes_current_token(): void
    {
        // Arrange: usuario + token Sanctum
        $user = User::create([
            'username' => 'john_doe',
            'email'    => 'john@example.com',
            'password' => Hash::make('secret-password'),
            'phone'    => '+54 11 1234 5678',
            'role'     => 'admin',
        ]);

        $newAccessToken = $user->createToken(
            'authToken',
            ['server:read', 'server:create', 'server:update', 'server:delete', 'server:restore']
        );

        $plainTextToken = $newAccessToken->plainTextToken;

        // Verificamos que el token exista antes del logout
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id'   => $user->id,
            'tokenable_type' => User::class,
        ]);

        // Act: enviamos el token como Bearer
        $response = $this->withHeader('Authorization', 'Bearer ' . $plainTextToken)
            ->postJson('/api/logout');

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente.',
                'data'    => null,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta',
                'errors',
            ]);

        // El token debe haber sido eliminado
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id'   => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    /**
     * Test authenticated user information is returned correctly.
     */
    public function test_user_endpoint_returns_authenticated_user_information(): void
    {
        // Arrange
        $user = User::create([
            'username' => 'john_doe',
            'email'    => 'john@example.com',
            'password' => Hash::make('secret-password'),
            'phone'    => '+54 11 1234 5678',
            'role'     => 'admin',
        ]);

        // Autenticamos al usuario en el contexto del test.
        // Si tu API usa el guard 'sanctum', podés usar actingAs($user, 'sanctum').
        $this->actingAs($user);

        // Act
        $response = $this->getJson('/api/user');

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Información del usuario obtenida exitosamente',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id_user',
                        'username',
                        'email',
                        'phone',
                        'role',
                    ],
                ],
                'meta',
                'errors',
            ]);

        $this->assertSame($user->id, $response->json('data.user.id_user'));
        $this->assertSame($user->username, $response->json('data.user.username'));
        $this->assertSame($user->email, $response->json('data.user.email'));
        $this->assertSame($user->phone, $response->json('data.user.phone'));
        $this->assertSame($user->role, $response->json('data.user.role'));
    }
}