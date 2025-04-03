<?php

namespace App\Services;

/**
 * Service for interacting with postcode data in the database.
 */
class PostcodeService
{
    private \PDO $pdo;

    /**
     * PostcodeService constructor.
     *
     * @param PDO $pdo PDO database connection
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves postcodes with optional filters and pagination.
     *
     * @param array $params Query parameters
     * @return array List of matching postcodes
     */
    public function getPostcodes(array $params): array
    {
        $limit = 50;
        $bindings = [];

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $offset = ($page - 1) * $limit;

        if (!empty($params['postcode'])) {
            $sql = "SELECT * FROM post_indexes 
                WHERE post_office_post_code LIKE :postcode
                ORDER BY post_office_post_code ASC
                LIMIT $limit OFFSET $offset";
            $bindings['postcode'] = '%' . $params['postcode'] . '%';
        } elseif (!empty($params['address'])) {
            $sql = "SELECT * FROM post_indexes 
                WHERE LOWER(settlement_ua) LIKE LOWER(:address) 
                ORDER BY settlement_ua ASC
                LIMIT $limit OFFSET $offset";
            $bindings['address'] = '%' . $params['address'] . '%';
        } else {
            $sql = "SELECT * FROM post_indexes 
                ORDER BY post_office_post_code ASC 
                LIMIT $limit OFFSET $offset";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return [
            'data' => $stmt->fetchAll(),
            'page' => $page,
        ];
    }

    /**
     * Adds or updates a list of postcodes.
     *
     * @param array $items List of validated postcode records
     * @return array Result status for each record
     */
    public function addPostcodes(array $items): array
    {
        $results = [];

        foreach ($items as $item) {
            if (empty($item['post_office_post_code'])) {
                $results[] = ['status' => 'error', 'message' => 'Missing post_office_post_code'];
                continue;
            }

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM post_indexes WHERE post_office_post_code = ?");
            $stmt->execute([$item['post_office_post_code']]);
            $exists = $stmt->fetchColumn() > 0;

            $fields = [
                'post_office_post_code', 'postal_code', 'region_ua', 'region_en',
                'district_old_ua', 'district_new_ua', 'district_new_en',
                'settlement_ua', 'settlement_en', 'post_office_ua', 'post_office_en'
            ];

            $data = [];
            foreach ($fields as $field) {
                $data[$field] = $item[$field] ?? null;
            }

            if ($exists) {
                $sql = "UPDATE post_indexes SET 
                postal_code = :postal_code,
                region_ua = :region_ua,
                region_en = :region_en,
                district_old_ua = :district_old_ua,
                district_new_ua = :district_new_ua,
                district_new_en = :district_new_en,
                settlement_ua = :settlement_ua,
                settlement_en = :settlement_en,
                post_office_ua = :post_office_ua,
                post_office_en = :post_office_en,
                added_via_api = TRUE,
                updated_at = CURRENT_TIMESTAMP
                WHERE post_office_post_code = :post_office_post_code";
            } else {
                $sql = "INSERT INTO post_indexes (
                post_office_post_code, postal_code,
                region_ua, region_en,
                district_old_ua, district_new_ua, district_new_en,
                settlement_ua, settlement_en,
                post_office_ua, post_office_en,
                added_via_api
            ) VALUES (
                :post_office_post_code, :postal_code,
                :region_ua, :region_en,
                :district_old_ua, :district_new_ua, :district_new_en,
                :settlement_ua, :settlement_en,
                :post_office_ua, :post_office_en,
                TRUE
            )";
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
            $results[] = ['status' => $exists ? 'updated' : 'inserted', 'data' => $item];
        }

        return $results;
    }

    /**
     * Deletes a postcode by its unique code.
     *
     * @param string $code Postcode ID to delete
     * @return array Deletion status message
     */
    public function deletePostcode(string $code): array
    {
        $stmt = $this->pdo->prepare("DELETE FROM post_indexes WHERE post_office_post_code = :code");
        $stmt->execute(['code' => $code]);

        if ($stmt->rowCount() === 0) {
            return ['status' => 'error', 'message' => 'Record not found'];
        }

        return ['status' => 'deleted', 'code' => $code];
    }
}