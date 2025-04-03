<?php

namespace App\Requests;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Validates incoming postcode data from HTTP requests.
 */
class PostcodeRequest
{
    /**
     * List of required fields for each postcode record.
     *
     * @var string[]
     */
    public static array $requiredFields = [
        'post_office_post_code', 'postal_code',
        'region_ua', 'region_en',
        'settlement_ua', 'settlement_en',
        'post_office_ua', 'post_office_en',
        'district_old_ua', 'district_new_ua',
        'district_new_en'
    ];

    /**
     * Validates multiple postcode records in a request.
     * Returns cleaned records if valid, or throws an exception with errors.
     *
     * @param ServerRequestInterface $request HTTP request containing postcodes
     * @return array Validated list of records
     * @throws \InvalidArgumentException If validation fails
     */
    public static function validateMany(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();
        $items = isset($body[0]) ? $body : [$body];

        $validated = [];
        $errors = [];

        foreach ($items as $i => $item) {
            $missing = [];

            foreach (self::$requiredFields as $field) {
                if (!isset($item[$field]) || trim($item[$field]) === '') {
                    $missing[] = $field;
                }
            }

            if (!empty($missing)) {
                $errors[] = [
                    'index' => $i,
                    'status' => 'error',
                    'missing_fields' => $missing
                ];
            } else {
                $clean = [];
                foreach (self::$requiredFields as $field) {
                    $clean[$field] = trim($item[$field]);
                }
                $validated[] = $clean;
            }
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors, JSON_UNESCAPED_UNICODE));
        }

        return $validated;
    }
}