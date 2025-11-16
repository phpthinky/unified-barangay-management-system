<?php
// FILE: app/Traits/HasEmailVerification.php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

trait HasEmailVerification
{
    /**
     * Generate a 6-digit verification code
     */
    public function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send email verification code
     */
    public function sendEmailVerificationCode(): bool
    {
        // Check rate limiting (max 1 email per 2 minutes)
        if ($this->email_verification_last_sent_at) {
            $lastSent = \Carbon\Carbon::parse($this->email_verification_last_sent_at);
            if ($lastSent->gt(now()->subMinutes(2))) {
                return false;
            }
        }

        // Generate new code
        $code = $this->generateVerificationCode();
        
        // Update user
        $this->update([
            'email_verification_token' => $code,
            'email_verification_token_expires_at' => now()->addMinutes(15), // 15 minutes validity
            'email_verification_last_sent_at' => now(),
        ]);

        // Send email
        try {
            Mail::to($this->email)->send(new EmailVerificationMail($this, $code));
            return true;
        } catch (\Exception $e) {
            \Log::error('Email verification send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify the email with provided code
     */
    public function verifyEmailWithCode(string $code): bool
    {
        // Check if code matches and hasn't expired
        if ($this->email_verification_token !== $code) {
            $this->increment('email_verification_attempts');
            return false;
        }

        if (!$this->email_verification_token_expires_at) {
            return false;
        }

        $expiresAt = \Carbon\Carbon::parse($this->email_verification_token_expires_at);
        if ($expiresAt->lt(now())) {
            return false;
        }

        // Mark as verified
        $this->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_token_expires_at' => null,
            'email_verification_attempts' => 0,
        ]);

        return true;
    }

    /**
     * Check if verification code is expired
     */
    public function isVerificationCodeExpired(): bool
    {
        if (!$this->email_verification_token_expires_at) {
            return true;
        }
        
        $expiresAt = \Carbon\Carbon::parse($this->email_verification_token_expires_at);
        return $expiresAt->lt(now());
    }

    /**
     * Check if user has exceeded verification attempts (max 5 attempts)
     */
    public function hasExceededVerificationAttempts(): bool
    {
        return $this->email_verification_attempts >= 5;
    }

    /**
     * Reset verification attempts
     */
    public function resetVerificationAttempts(): void
    {
        $this->update(['email_verification_attempts' => 0]);
    }

    /**
     * Check if user can resend verification code
     */
    public function canResendVerificationCode(): bool
    {
        if (!$this->email_verification_last_sent_at) {
            return true;
        }
        
        $lastSent = \Carbon\Carbon::parse($this->email_verification_last_sent_at);
        return $lastSent->lte(now()->subMinutes(2));
    }

    /**
     * Get seconds until user can resend verification code
     */
    public function secondsUntilCanResend(): int
    {
        if (!$this->email_verification_last_sent_at) {
            return 0;
        }

        $lastSent = \Carbon\Carbon::parse($this->email_verification_last_sent_at);
        $nextAllowedAt = $lastSent->copy()->addMinutes(2);
        $diff = now()->diffInSeconds($nextAllowedAt, false);

        return max(0, $diff);
    }
}