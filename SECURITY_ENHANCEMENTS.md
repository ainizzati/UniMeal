# Security Enhancements - UniMeal System

## Overview
This document details the security enhancements implemented for the UniMeal system, focusing on **Authentication** and **Authorization** best practices.

---

## II. AUTHENTICATION ENHANCEMENTS

### 1. Stronger Password Policies

**Implementation Method**: Laravel Password Validation Rules with `Password::defaults()`

**Files Modified**:
- [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php#L24-L31)

**Security Features Implemented**:
- **Minimum 10 characters** (increased from default 8)
- **Mixed case requirement** (uppercase + lowercase letters)
- **Numbers required**
- **Symbols required**
- **Compromised password check** using `Have I Been Pwned` API
  - Checks if password appears in known data breaches
  - Uses k-anonymity model (only sends first 5 chars of hash)
  - Automatically rejects commonly compromised passwords

**Best Practice Reference**: NIST SP 800-63B - Password Complexity Requirements

**How It Works**:
```php
Password::defaults(function () {
    return Password::min(12)
        ->letters()       // Requires letters
        ->mixedCase()     // Requires both upper and lowercase
        ->numbers()       // Requires at least one number
        ->symbols()       // Requires at least one symbol
        ->uncompromised(); // Checks against breach database
});
```

**User Impact**:
- All new registrations (Student and Cafeteria) must meet these requirements
- Password reset operations enforce these rules
- Existing passwords are grandfathered until next password change

---

### 3. Login Attempt Monitoring

**Implementation Method**: Database-backed login attempt logging with IP tracking

**Files Created**:
- [database/migrations/2025_12_26_165842_create_login_attempts_table.php](database/migrations/2025_12_26_165842_create_login_attempts_table.php)
- [app/Models/LoginAttempt.php](app/Models/LoginAttempt.php)

**Files Modified**:
- [app/Http/Controllers/Auth/StudentAuthController.php](app/Http/Controllers/Auth/StudentAuthController.php#L41-L96)

**Security Features Implemented**:
- **Comprehensive logging** of all login attempts (successful and failed)
- **IP address tracking** for each attempt
- **User agent logging** for device identification
- **Timestamp recording** for forensic analysis
- **Guard-specific tracking** (student vs cafeteria)
- **Automatic cleanup** of failed attempts on successful login

**Database Schema**:
```sql
- email (string)
- guard (string) - student/cafeteria/web
- ip_address (string, max 45 chars for IPv6)
- user_agent (text)
- successful (boolean)
- attempted_at (timestamp)
- Indexed fields for performance
```

**Best Practice Reference**:
- OWASP Authentication Cheat Sheet - Login Attempt Logging
- PCI DSS Requirement 10.2.4 - Track authentication attempts

**Benefits**:
- Security team can identify brute force attacks
- Detect credential stuffing attempts
- Track suspicious activity patterns
- Generate security reports and alerts

---

### 7. Account Lockout Mechanism

**Implementation Method**: Progressive lockout based on failed attempt count

**Files Modified**:
- [app/Models/LoginAttempt.php](app/Models/LoginAttempt.php#L42-L49) - `getRecentFailedAttempts()` method
- [app/Http/Controllers/Auth/StudentAuthController.php](app/Http/Controllers/Auth/StudentAuthController.php#L68-L76)

**Lockout Policy**:
- **Threshold**: 5 failed login attempts
- **Lockout Duration**: 15 minutes
- **Scope**: Per email address + guard combination
- **Reset Condition**: Automatic after successful login OR timeout expiry

**Security Features**:
- **Time-window based**: Only counts attempts in last 15 minutes
- **Automatic expiration**: Old failed attempts don't count
- **Clear user feedback**: Informative error message
- **Separate guard tracking**: Student and Cafeteria lockouts are independent
- **Preserves user input**: Email field retained on error for UX

**How It Works**:
1. Before checking credentials, count recent failed attempts
2. If count >= 5 in last 15 minutes, deny login
3. Log the blocked attempt
4. Display lockout message to user
5. On successful login, clear all failed attempts for that email/guard

**Error Message**:
```
"Too many failed login attempts. Your account is temporarily locked.
Please try again in 15 minutes."
```

**Best Practice Reference**:
- OWASP Authentication Cheat Sheet - Account Lockout
- CWE-307: Improper Restriction of Excessive Authentication Attempts

**Security Benefits**:
- **Prevents brute force attacks**
- **Mitigates credential stuffing**
- **Limits password guessing attempts**
- **Reduces automated attack success**

---

### 5. Session Security Hardening

**Implementation Method**: Enhanced session configuration with secure cookie settings

**Files Modified**:
- [config/session.php](config/session.php#L35) - Default session lifetime reduced to 60 minutes
- [config/session.php](config/session.php#L202) - SameSite changed to 'strict'

**Files Created**:
- [.env.security.example](.env.security.example) - Recommended production settings

**Security Configurations**:

#### Session Lifetime
- **Reduced from 120 to 60 minutes**
- Limits exposure window if session is compromised
- Forces re-authentication more frequently

#### Session Driver
- **Changed to database** (from file)
- Better tracking of active sessions
- Easier to revoke sessions programmatically
- Enables "logout other devices" feature

#### SameSite Cookie Attribute
- **Set to 'strict'** (from 'lax')
- Prevents CSRF attacks via cross-site requests
- Cookie only sent with same-site requests
- Blocks cookie in cross-origin navigation

#### HTTP-Only Cookies
- **Enabled by default**
- Prevents JavaScript access to session cookies
- Mitigates XSS-based session theft

#### Secure Cookie Flag
- **Configurable via SESSION_SECURE_COOKIE**
- Should be `true` in production (requires HTTPS)
- Set to `false` in local development

#### Session Regeneration
- **Implemented on login** ([StudentAuthController.php](app/Http/Controllers/Auth/StudentAuthController.php#L87))
- Prevents session fixation attacks
- Generates new session ID after authentication

**Configuration Summary**:
```env
SESSION_DRIVER=database          # Better session management
SESSION_LIFETIME=60              # 1 hour instead of 2
SESSION_SECURE_COOKIE=true       # HTTPS only (production)
SESSION_HTTP_ONLY=true           # No JavaScript access
SESSION_SAME_SITE=strict         # Strict CSRF protection
```

**Best Practice References**:
- OWASP Session Management Cheat Sheet
- CWE-384: Session Fixation
- CWE-352: Cross-Site Request Forgery (CSRF)

**Attack Mitigations**:
- ✅ Session Fixation (via regeneration)
- ✅ CSRF (via SameSite strict)
- ✅ XSS Session Theft (via HTTP-only)
- ✅ Man-in-the-Middle (via Secure flag in production)
- ✅ Session Hijacking (via reduced lifetime)

---

## Additional Authentication Features

### Unified Login for Multiple Guards
**Files Modified**: [app/Http/Controllers/Auth/StudentAuthController.php](app/Http/Controllers/Auth/StudentAuthController.php#L50-L66)

**Enhancement**:
- Single login endpoint handles both Student and Cafeteria authentication
- Automatically detects user type based on email
- Routes to appropriate dashboard after login
- Maintains separate session guards for authorization

**Security Benefit**:
- Consistent security policy across all user types
- Centralized authentication logic
- Easier to audit and maintain

---

## Testing the Enhancements

### Manual Testing Checklist

#### 1. Password Policy Testing
- [ ] Try registering with password < 12 characters (should fail)
- [ ] Try password without uppercase (should fail)
- [ ] Try password without numbers (should fail)
- [ ] Try password without symbols (should fail)
- [ ] Try common password like "Password123!" (should fail if compromised)
- [ ] Register with strong password like "MyStr0ng!P@ssw0rd" (should succeed)

#### 2. Login Attempt Monitoring
- [ ] Check `login_attempts` table after successful login
- [ ] Verify IP address and user agent are logged
- [ ] Confirm `successful=1` for valid login
- [ ] Confirm `successful=0` for failed login

#### 3. Account Lockout Testing
- [ ] Attempt login with wrong password 5 times
- [ ] Verify account is locked on 6th attempt
- [ ] Wait 15 minutes and verify can login again
- [ ] Confirm failed attempts are cleared after successful login

#### 4. Session Security Testing
- [ ] Verify session cookie has HttpOnly flag (check browser DevTools)
- [ ] Verify session cookie has SameSite=Strict
- [ ] Confirm session expires after 60 minutes of inactivity
- [ ] Test that session ID changes after login (check cookie value before/after)

### Database Verification

```sql
-- Check login attempts
SELECT * FROM login_attempts ORDER BY attempted_at DESC LIMIT 10;

-- Check failed attempts for specific email
SELECT * FROM login_attempts
WHERE email = 'test@example.com'
AND successful = 0
AND attempted_at >= NOW() - INTERVAL 15 MINUTE;

-- Check sessions table (if using database driver)
SELECT * FROM sessions ORDER BY last_activity DESC LIMIT 10;
```

---

## Production Deployment Checklist

Before deploying to production:

1. **Update .env file**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   SESSION_DRIVER=database
   SESSION_LIFETIME=60
   SESSION_SECURE_COOKIE=true  # IMPORTANT: Requires HTTPS
   SESSION_HTTP_ONLY=true
   SESSION_SAME_SITE=strict
   ```

2. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```

3. **Clear caches**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   ```

4. **Enable HTTPS**:
   - Ensure SSL/TLS certificate is installed
   - Configure web server to redirect HTTP to HTTPS
   - Set `SESSION_SECURE_COOKIE=true`

5. **Test all authentication flows**:
   - Student registration and login
   - Cafeteria registration and login
   - Password reset
   - Account lockout behavior

---

## Security Standards Compliance

These enhancements align with:

- **OWASP Top 10 2021**
  - A07:2021 - Identification and Authentication Failures

- **NIST Cybersecurity Framework**
  - PR.AC-1: Identity and credential management
  - DE.CM-1: Network monitoring for anomalous activity

- **PCI DSS 4.0**
  - Requirement 8: Strong authentication and access control
  - Requirement 10: Logging and monitoring

- **CWE (Common Weakness Enumeration)**
  - CWE-307: Improper Restriction of Excessive Authentication Attempts
  - CWE-384: Session Fixation
  - CWE-521: Weak Password Requirements

---

## Maintenance and Monitoring

### Recommended Monitoring

1. **Login Attempt Alerts**:
   - Set up alerts for high failed attempt rates
   - Monitor for distributed brute force attacks
   - Track lockout frequency by IP

2. **Session Management**:
   - Monitor session table growth
   - Clean up old sessions regularly
   - Track concurrent session counts

3. **Password Security**:
   - Audit password change frequency
   - Monitor for compromised password attempts
   - Track password reset requests

### Database Cleanup

Add to scheduled tasks in [app/Console/Kernel.php](app/Console/Kernel.php):

```php
// Clean up old login attempts (older than 30 days)
$schedule->command('cleanup:login-attempts')->daily();

// Clean up expired sessions
$schedule->command('session:gc')->daily();
```

---

## Future Enhancement Recommendations

1. **Multi-Factor Authentication (2FA)**
   - Extend to Student and Cafeteria models
   - Already available for User model via Fortify

2. **Email Notifications**
   - Send alerts for suspicious login attempts
   - Notify on account lockout
   - Alert on password changes

3. **Progressive Delays**
   - Implement exponential backoff for failed attempts
   - Increase delay with each failed attempt

4. **CAPTCHA Integration**
   - Add reCAPTCHA after 3 failed attempts
   - Prevent automated attacks

5. **Passwordless Authentication**
   - Magic link login option
   - WebAuthn/FIDO2 support

---

## Support and Questions

For questions about these security enhancements:
1. Review the implementation files listed in each section
2. Check Laravel documentation for Fortify and authentication
3. Refer to OWASP guidelines for security best practices

**Last Updated**: December 26, 2025
