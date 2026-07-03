<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\ForgotPasswordOtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordOtpTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_request_an_otp_and_reset_their_password()
    {
        Mail::fake();

        // 1. Create a user
        $user = User::create([
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => bcrypt('old_password'),
            'role'     => 'customer',
            'status'   => 'active',
        ]);

        // 2. Request OTP
        $response = $this->post(route('password.email'), [
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect(route('password.otp.show'));
        $this->assertEquals('john@example.com', session('reset_email'));

        // 3. Assert OTP was stored in database and email sent
        $tokenRow = DB::table('password_reset_tokens')->where('email', 'john@example.com')->first();
        $this->assertNotNull($tokenRow);
        $this->assertEquals(6, strlen($tokenRow->token));

        Mail::assertSent(ForgotPasswordOtpMail::class, function ($mail) use ($tokenRow) {
            return $mail->hasTo('john@example.com') && $mail->otp === $tokenRow->token;
        });

        // 4. Verify invalid OTP
        $response = $this->post(route('password.otp.verify'), [
            'otp' => '000000',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid or expired OTP code.');
        $this->assertNull(session('otp_verified'));

        // 5. Verify valid OTP
        $response = $this->post(route('password.otp.verify'), [
            'otp' => $tokenRow->token,
        ]);
        $response->assertRedirect(route('password.reset.otp'));
        $this->assertTrue(session('otp_verified'));

        // 6. Reset password
        $response = $this->post(route('password.update.otp'), [
            'password'              => 'new_secure_password',
            'password_confirmation' => 'new_secure_password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionMissing('reset_email');
        $response->assertSessionMissing('otp_verified');

        // 7. Verify the user can login with the new password
        $user->refresh();
        $this->assertTrue(password_verify('new_secure_password', $user->password));

        // 8. Assert token was deleted
        $this->assertNull(DB::table('password_reset_tokens')->where('email', 'john@example.com')->first());
    }
}
