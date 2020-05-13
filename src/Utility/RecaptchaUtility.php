<?php
namespace App\Utility;

class RecaptchaUtility extends Singleton {
    protected string $siteKey = '';
    protected string $privateKey = '';

    protected function initializeInstance() {
        $this->siteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? $this->siteKey;
        $this->privateKey = $_ENV['RECAPTCHA_PRIVATE_KEY'] ?? $this->privateKey;
    }

    public function getSiteKey(): string {
        return $this->siteKey;
    }

    public function verify(string $token) {
        $postData = [
            'secret' => $this->privateKey,
            'response' => $token,
        ];

        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        if (!empty($postData)) {
            curl_setopt($curlHandler, CURLOPT_POST, 1);
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
        $result = curl_exec($curlHandler);
        curl_close($curlHandler);

        if (empty($result) || $result === false) {
            return false;
        }

        $obj = json_decode($result);
        if (is_object($obj) && isset($obj->success)) {
            return $obj->success;
        }
        return false;
    }
}
