<?php

/**
 * PromptPayAPI class — extracted from the original index.php
 * to keep the HTTP entry point thin. Behaviour is preserved.
 */

class PromptPayAPI {
    
    const ID_PAYLOAD_FORMAT = '00';
    const ID_POI_METHOD = '01';
    const ID_MERCHANT_INFORMATION_BOT = '29';
    const ID_TRANSACTION_CURRENCY = '53';
    const ID_TRANSACTION_AMOUNT = '54';
    const ID_COUNTRY_CODE = '58';
    const ID_CRC = '63';
    
    const PAYLOAD_FORMAT_EMV_QRCPS_MERCHANT_PRESENTED_MODE = '01';
    const POI_METHOD_STATIC = '11';
    const POI_METHOD_DYNAMIC = '12';
    const MERCHANT_INFORMATION_TEMPLATE_ID_GUID = '00';
    const BOT_ID_MERCHANT_PHONE_NUMBER = '01';
    const BOT_ID_MERCHANT_TAX_ID = '02';
    const BOT_ID_MERCHANT_EWALLET_ID = '03';
    const GUID_PROMPTPAY = 'A000000677010111';
    const TRANSACTION_CURRENCY_THB = '764';
    const COUNTRY_CODE_TH = 'TH';

    public function generatePayload($target, $amount = null) {
        $target = $this->sanitizeTarget($target);
        $targetType = strlen($target) >= 15 ? self::BOT_ID_MERCHANT_EWALLET_ID : 
                     (strlen($target) >= 13 ? self::BOT_ID_MERCHANT_TAX_ID : self::BOT_ID_MERCHANT_PHONE_NUMBER);

        $data = [
            $this->f(self::ID_PAYLOAD_FORMAT, self::PAYLOAD_FORMAT_EMV_QRCPS_MERCHANT_PRESENTED_MODE),
            $this->f(self::ID_POI_METHOD, $amount ? self::POI_METHOD_DYNAMIC : self::POI_METHOD_STATIC),
            $this->f(self::ID_MERCHANT_INFORMATION_BOT, $this->serialize([
                $this->f(self::MERCHANT_INFORMATION_TEMPLATE_ID_GUID, self::GUID_PROMPTPAY),
                $this->f($targetType, $this->formatTarget($target))
            ])),
            $this->f(self::ID_COUNTRY_CODE, self::COUNTRY_CODE_TH),
            $this->f(self::ID_TRANSACTION_CURRENCY, self::TRANSACTION_CURRENCY_THB),
        ];
        
        if ($amount !== null && $amount > 0) {
            array_push($data, $this->f(self::ID_TRANSACTION_AMOUNT, $this->formatAmount($amount)));
        }
        
        $dataToCrc = $this->serialize($data) . self::ID_CRC . '04';
        array_push($data, $this->f(self::ID_CRC, $this->crc16($dataToCrc)));
        
        return $this->serialize($data);
    }

    private function f($id, $value) {
        return implode('', [$id, substr('00' . strlen($value), -2), $value]);
    }
    
    private function serialize($xs) {
        return implode('', $xs);
    }
    
    private function sanitizeTarget($str) {
        return preg_replace('/[^0-9]/', '', $str);
    }

    private function formatTarget($target) {
        $str = $this->sanitizeTarget($target);
        if (strlen($str) >= 13) {
            return $str;
        }
        
        $str = preg_replace('/^0/', '66', $str);
        $str = '0000000000000' . $str;
        
        return substr($str, -13);
    }

    private function formatAmount($amount) {
        return number_format($amount, 2, '.', '');
    }

    private function crc16($data) {
        $crc = 0xFFFF;
        $polynomial = 0x1021;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ $polynomial;
                } else {
                    $crc = $crc << 1;
                }
            }
        }
        
        return strtoupper(dechex($crc & 0xFFFF));
    }

    /**
     * Generate QR code using goQR.me API (no local dependencies)
     * Note: Requires internet connection
     */
    public function generateQrCodeUrl($target, $amount = null, $size = 300) {
        $payload = $this->generatePayload($target, $amount);
        
        // Use goQR.me API with better parameters
        $params = [
            'data' => $payload,
            'size' => $size . 'x' . $size,
            'ecc' => 'M',  // Medium error correction (15% redundancy)
            'format' => 'png',
            'qzone' => 1,  // Quiet zone for better scanning
            'charset-source' => 'UTF-8',
            'charset-target' => 'UTF-8'
        ];
        
        return "https://api.qrserver.com/v1/create-qr-code/?" . http_build_query($params);
    }
    
    /**
     * Generate and save QR code using goQR.me API
     */
    public function generateQrCodeFile($savePath, $target, $amount = null, $size = 300) {
        $qrUrl = $this->generateQrCodeUrl($target, $amount, $size);
        
        // Use cURL for better error handling
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $qrUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $qrData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($qrData === false || !empty($error) || $httpCode !== 200) {
            throw new Exception('Failed to generate QR code: ' . ($error ?: 'HTTP ' . $httpCode));
        }
        
        return file_put_contents($savePath, $qrData);
    }

    /**
     * Generate and return QR code image data directly
     */
    public function generateQrCodeData($target, $amount = null, $size = 300) {
        $qrUrl = $this->generateQrCodeUrl($target, $amount, $size);
        
        // Use cURL for better error handling
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $qrUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $qrData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($qrData === false || !empty($error) || $httpCode !== 200) {
            throw new Exception('Failed to generate QR code: ' . ($error ?: 'HTTP ' . $httpCode));
        }
        
        return $qrData;
    }
}
