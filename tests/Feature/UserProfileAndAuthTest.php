<?php

namespace Tests\Feature;

use App\Mail\UserCreatedMail;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UserProfileAndAuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('email', 'admin@openproject.local')->first();
        $this->regularUser = User::where('email', 'alex@openproject.local')->first();
    }

    public function test_admin_creates_user_and_dispatches_welcome_email(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Sara Connor',
            'email' => 'sara@example.com',
            'password' => 'secretP@ssw0rd!',
            'status' => 'active',
            'send_welcome_email' => 1,
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'sara@example.com']);

        Mail::assertSent(UserCreatedMail::class, function (UserCreatedMail $mail) {
            return $mail->hasTo('sara@example.com')
                && $mail->plainPassword === 'secretP@ssw0rd!'
                && str_contains($mail->loginUrl, 'login');
        });
    }

    public function test_admin_can_opt_out_of_welcome_email(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Silent User',
            'email' => 'silent@example.com',
            'password' => 'secretP@ssw0rd!',
            'status' => 'active',
            'send_welcome_email' => 0,
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'silent@example.com']);

        Mail::assertNotSent(UserCreatedMail::class);
    }

    public function test_user_can_view_profile_edit_screen(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($this->regularUser->name);
        $response->assertSee($this->regularUser->email);
        $response->assertSee('Change Password');
    }

    public function test_user_can_update_profile_name_and_email(): void
    {
        $response = $this->actingAs($this->regularUser)->patch('/profile', [
            'name' => 'Alex Updated',
            'email' => 'alex_new@openproject.local',
        ]);

        $response->assertRedirect('/profile');
        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id,
            'name' => 'Alex Updated',
            'email' => 'alex_new@openproject.local',
        ]);
    }

    public function test_user_cannot_update_profile_with_taken_email(): void
    {
        $response = $this->actingAs($this->regularUser)->patch('/profile', [
            'name' => 'Alex Test',
            'email' => $this->admin->email,
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $response = $this->actingAs($this->regularUser)->put('/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'newPassword123!',
            'password_confirmation' => 'newPassword123!',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $response = $this->actingAs($this->regularUser)->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'brandNewSecurePass123!',
            'password_confirmation' => 'brandNewSecurePass123!',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('success');

        $this->regularUser->refresh();
        $this->assertTrue(Hash::check('brandNewSecurePass123!', $this->regularUser->password));
    }

    public function test_guest_can_view_forgot_password_screen(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Forgot your password?');
        $response->assertSee('Email Password Reset Link');
    }

    public function test_guest_can_request_password_reset_link(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => $this->regularUser->email,
        ]);

        $response->assertSessionHas('status');

        Notification::assertSentTo(
            $this->regularUser,
            ResetPassword::class
        );
    }

    public function test_guest_can_view_reset_password_screen_with_token(): void
    {
        $token = Password::broker()->createToken($this->regularUser);

        $response = $this->get('/reset-password/'.$token.'?email='.$this->regularUser->email);

        $response->assertStatus(200);
        $response->assertSee('Set new password');
        $response->assertSee($this->regularUser->email);
    }

    public function test_guest_can_reset_password_with_valid_token(): void
    {
        $token = Password::broker()->createToken($this->regularUser);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $this->regularUser->email,
            'password' => 'superResetPass99!',
            'password_confirmation' => 'superResetPass99!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->regularUser);

        $this->regularUser->refresh();
        $this->assertTrue(Hash::check('superResetPass99!', $this->regularUser->password));
    }
}
