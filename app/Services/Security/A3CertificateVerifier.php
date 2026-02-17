<?php

namespace App\Services\Security;

use Illuminate\Http\Request;

class A3CertificateVerifier
{
    public function verify(Request $request): array
    {
        if (!config('backup.a3_required', true)) {
            return ['valid' => true, 'mode' => 'disabled'];
        }

        if (
            config('backup.a3_allow_bypass', false)
            && $request->headers->get('X-A3-Test-Bypass') === '1'
        ) {
            return ['valid' => true, 'mode' => 'bypass'];
        }

        $verify = strtoupper((string) $this->readHeader($request, [
            'SSL_CLIENT_VERIFY',
            'X-SSL-Client-Verify',
        ]));

        $subject = (string) $this->readHeader($request, [
            'SSL_CLIENT_S_DN',
            'X-SSL-Client-S-DN',
        ]);

        $issuer = (string) $this->readHeader($request, [
            'SSL_CLIENT_I_DN',
            'X-SSL-Client-I-DN',
        ]);

        if ($verify !== 'SUCCESS' || $subject === '') {
            return ['valid' => false, 'reason' => 'invalid_client_certificate'];
        }

        $issuerRegex = (string) config('backup.a3_allowed_issuer_regex', '');
        if ($issuerRegex !== '' && @preg_match($issuerRegex, $issuer) !== 1) {
            return ['valid' => false, 'reason' => 'invalid_certificate_issuer'];
        }

        return [
            'valid' => true,
            'mode' => 'certificate',
            'subject' => $subject,
            'issuer' => $issuer,
            'serial' => (string) $this->readHeader($request, ['SSL_CLIENT_M_SERIAL', 'X-SSL-Client-M-Serial']),
        ];
    }

    private function readHeader(Request $request, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $request->headers->get($key);
            if (!empty($value)) {
                return (string) $value;
            }

            $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
            $serverValue = $request->server->get($serverKey);
            if (!empty($serverValue)) {
                return (string) $serverValue;
            }

            $nativeServerValue = $request->server->get($key);
            if (!empty($nativeServerValue)) {
                return (string) $nativeServerValue;
            }
        }

        return null;
    }
}
