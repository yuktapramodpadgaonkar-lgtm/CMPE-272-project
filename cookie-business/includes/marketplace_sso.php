<?php
/**
 * Cross-domain SSO: verify signed tokens from OurMarketplace and mirror user identity in PHP session.
 */

function marketplace_sso_b64url_encode(string $bin): string {
    return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
}

function marketplace_sso_b64url_decode(string $b64): string {
    $pad = strlen($b64) % 4;
    if ($pad > 0) {
        $b64 .= str_repeat('=', 4 - $pad);
    }
    $decoded = base64_decode(strtr($b64, '-_', '+/'), true);
    return $decoded === false ? '' : $decoded;
}

function marketplace_sso_load_secret(): ?string {
    $path = __DIR__ . '/sso_config.php';
    if (!is_file($path)) {
        return null;
    }
    require_once $path;
    if (!defined('MARKETPLACE_SSO_SECRET')) {
        return null;
    }
    $s = (string) MARKETPLACE_SSO_SECRET;
    if ($s === '' || $s === 'CHANGE_ME_TO_A_LONG_RANDOM_STRING_SHARED_WITH_MARKETPLACE') {
        return null;
    }
    return $s;
}

function marketplace_sso_expected_issuer(): string {
    if (defined('MARKETPLACE_SSO_ISSUER')) {
        return (string) MARKETPLACE_SSO_ISSUER;
    }
    return 'ourmarketplace';
}

/**
 * @return array<string, mixed>|null Verified claims or null
 */
function marketplace_sso_verify_token(string $token): ?array {
    $token = trim($token);
    if ($token === '' || strpos($token, '.') === false) {
        return null;
    }
    $parts = explode('.', $token, 2);
    if (count($parts) !== 2) {
        return null;
    }
    list($payloadB64, $sigB64) = $parts;
    $secret = marketplace_sso_load_secret();
    if ($secret === null) {
        return null;
    }
    $expectedSig = marketplace_sso_b64url_encode(hash_hmac('sha256', $payloadB64, $secret, true));
    if (!hash_equals($expectedSig, $sigB64)) {
        return null;
    }
    $json = marketplace_sso_b64url_decode($payloadB64);
    if ($json === '') {
        return null;
    }
    $data = json_decode($json, true);
    if (!is_array($data)) {
        return null;
    }
    $now = time();
    if (!isset($data['exp']) || (int) $data['exp'] < $now) {
        return null;
    }
    if (!isset($data['iat']) || (int) $data['iat'] > $now + 120) {
        return null;
    }
    if (($data['iss'] ?? '') !== marketplace_sso_expected_issuer()) {
        return null;
    }
    $uid = isset($data['sub']) ? (int) $data['sub'] : 0;
    if ($uid <= 0) {
        return null;
    }
    return $data;
}

function marketplace_sso_start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function marketplace_sso_is_logged_in(): bool {
    marketplace_sso_start_session();
    return !empty($_SESSION['mp_sso_ok']) && $_SESSION['mp_sso_ok'] === true;
}

function marketplace_sso_display_name(): string {
    marketplace_sso_start_session();
    $name = isset($_SESSION['mp_full_name']) ? trim((string) $_SESSION['mp_full_name']) : '';
    if ($name !== '') {
        return $name;
    }
    $u = isset($_SESSION['mp_username']) ? trim((string) $_SESSION['mp_username']) : '';
    return $u !== '' ? $u : 'Marketplace user';
}

function marketplace_sso_clear(): void {
    marketplace_sso_start_session();
    unset(
        $_SESSION['mp_sso_ok'],
        $_SESSION['mp_user_id'],
        $_SESSION['mp_username'],
        $_SESSION['mp_full_name']
    );
}

/**
 * @param array<string, mixed> $claims
 */
function marketplace_sso_establish_session(array $claims): void {
    marketplace_sso_start_session();
    $_SESSION['mp_sso_ok']     = true;
    $_SESSION['mp_user_id']    = (int) ($claims['sub'] ?? 0);
    $_SESSION['mp_username']  = (string) ($claims['username'] ?? '');
    $_SESSION['mp_full_name']  = (string) ($claims['full_name'] ?? '');
}
