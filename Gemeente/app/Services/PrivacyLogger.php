<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PrivacyLogger
{
    /**
     * Log user action without PII.
     *
     * @param  array<string, mixed>  $context
     */
    public static function logUserAction(string $action, array $context = []): void
    {
        $sanitizedContext = self::sanitizeContext($context);

        Log::channel('privacy_safe')->info("User action: {$action}", $sanitizedContext);
    }

    /**
     * Log security event.
     */
    /**
     * @param  array<string, mixed>  $context
     */
    public static function logSecurityEvent(string $event, array $context = []): void
    {
        $sanitizedContext = self::sanitizeContext($context);
        $sanitizedContext['ip_hash'] = self::hashIp(request()->ip());
        $sanitizedContext['user_agent_hash'] = self::hashUserAgent(request()->userAgent());

        Log::channel('security')->warning("Security event: {$event}", $sanitizedContext);
    }

    /**
     * Log audit event.
     */
    /**
     * @param  array<string, mixed>  $context
     */
    public static function logAudit(string $action, array $context = []): void
    {
        $sanitizedContext = self::sanitizeContext($context);
        $sanitizedContext['timestamp'] = now()->toISOString();

        if (auth()->check()) {
            $sanitizedContext['user_id'] = auth()->id();
            $sanitizedContext['user_role'] = auth()->user()->getRoleNames()->first();
        }

        Log::channel('audit')->info("Audit: {$action}", $sanitizedContext);
    }

    /**
     * Log complaint action without exposing personal data.
     */
    /**
     * @param  array<string, mixed>  $context
     */
    public static function logComplaintAction(string $action, int $complaintId, array $context = []): void
    {
        $sanitizedContext = array_merge([
            'complaint_id' => $complaintId,
            'action' => $action,
        ], self::sanitizeContext($context));

        Log::channel('privacy_safe')->info("Complaint {$action}", $sanitizedContext);
    }

    /**
     * Sanitize context data to remove PII.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private static function sanitizeContext(array $context): array
    {
        $sanitized = [];

        foreach ($context as $key => $value) {
            // Skip keys that might contain PII
            if (self::isPotentialPII($key)) {
                continue;
            }

            // Hash values that might be PII
            if (self::shouldHashValue($key, $value)) {
                $sanitized[$key.'_hash'] = self::hashValue($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Check if a key might contain PII.
     */
    private static function isPotentialPII(string $key): bool
    {
        $piiKeys = [
            'email', 'name', 'phone', 'address', 'reporter_name',
            'reporter_email', 'reporter_phone', 'description',
            'internal_notes', 'password', 'token',
        ];

        $key = strtolower($key);

        foreach ($piiKeys as $piiKey) {
            if (str_contains($key, $piiKey)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a value should be hashed instead of logged directly.
     */
    private static function shouldHashValue(string $key, mixed $value): bool
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        $hashKeys = ['ip', 'user_agent', 'session_id'];
        $key = strtolower($key);

        foreach ($hashKeys as $hashKey) {
            if (str_contains($key, $hashKey)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Hash IP address for privacy.
     */
    private static function hashIp(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }

        return substr(hash('sha256', $ip.config('app.key')), 0, 16);
    }

    /**
     * Hash User Agent for privacy.
     */
    private static function hashUserAgent(?string $userAgent): ?string
    {
        if (! $userAgent) {
            return null;
        }

        return substr(hash('sha256', $userAgent.config('app.key')), 0, 16);
    }

    /**
     * Hash arbitrary value.
     */
    private static function hashValue(mixed $value): string
    {
        return substr(hash('sha256', (string) $value.config('app.key')), 0, 16);
    }

    /**
     * Log failed login attempt without exposing the attempted credentials.
     */
    public static function logFailedLogin(?string $email = null): void
    {
        self::logSecurityEvent('failed_login_attempt', [
            'email_domain' => $email ? Str::after($email, '@') : null,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log successful login.
     */
    public static function logSuccessfulLogin(): void
    {
        self::logSecurityEvent('successful_login', [
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log data export request (GDPR).
     */
    public static function logDataExport(int $userId): void
    {
        self::logAudit('data_export_requested', [
            'subject_user_id' => $userId,
            'requested_by' => auth()->id(),
        ]);
    }

    /**
     * Log data deletion request (GDPR).
     */
    public static function logDataDeletion(int $userId, string $reason = 'user_request'): void
    {
        self::logAudit('data_deletion_requested', [
            'subject_user_id' => $userId,
            'requested_by' => auth()->id(),
            'reason' => $reason,
        ]);
    }
}
