<?php


namespace ConferenceTools\Attendance\Service;

/**
 * Based on https://raw.githubusercontent.com/stripe/stripe-php/master/lib/WebhookSignature.php
 */
abstract class StripeSignatureValidator
{
    const EXPECTED_SCHEME = "v1";

    public static function verifyHeader(string $payload, string $header, string $secret, ?int $tolerance = null): bool
    {
        // Extract timestamp and signatures from header
        $timestamp = self::getTimestamp($header);
        $signatures = self::getSignatures($header, self::EXPECTED_SCHEME);
        if ($timestamp == -1) {
            throw new \RuntimeException(
                "Unable to extract timestamp and signatures from header"
            );
        }
        if (empty($signatures)) {
            throw new \RuntimeException(
                "No signatures found with expected scheme"
            );
        }

        // Check if expected signature is found in list of signatures from
        // header
        $signedPayload = "$timestamp.$payload";
        $expectedSignature = self::computeSignature($signedPayload, $secret);
        $signatureFound = false;
        foreach ($signatures as $signature) {
            if (hash_equals($expectedSignature, $signature)) {
                $signatureFound = true;
                break;
            }
        }
        if (!$signatureFound) {
            throw new \RuntimeException(
                "No signatures found matching the expected signature for payload"
            );
        }

        // Check if timestamp is within tolerance
        if (($tolerance > 0) && (abs(time() - $timestamp) > $tolerance)) {
            throw new \RuntimeException(
                "Timestamp outside the tolerance zone"
            );
        }

        return true;
    }

    private static function getTimestamp(string $header): int
    {
        $items = explode(",", $header);

        foreach ($items as $item) {
            $itemParts = explode("=", $item, 2);
            if ($itemParts[0] == "t") {
                if (!is_numeric($itemParts[1])) {
                    return -1;
                }
                return intval($itemParts[1]);
            }
        }

        return -1;
    }

    private static function getSignatures(string $header, string $scheme): array
    {
        $signatures = [];
        $items = explode(",", $header);

        foreach ($items as $item) {
            $itemParts = explode("=", $item, 2);
            if ($itemParts[0] == $scheme) {
                array_push($signatures, $itemParts[1]);
            }
        }

        return $signatures;
    }

    private static function computeSignature(string $payload, string $secret): string
    {
        return hash_hmac("sha256", $payload, $secret);
    }
}