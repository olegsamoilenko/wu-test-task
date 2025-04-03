-- Creates the table to store Ukrainian post office index data
-- Primary key is post_office_post_code (unique identifier)
-- Fields include localized names and metadata for tracking API origin and timestamps

CREATE TABLE post_indexes
(
    post_office_post_code VARCHAR(10) PRIMARY KEY,
    postal_code           VARCHAR(10),
    region_ua             VARCHAR(100),
    region_en             VARCHAR(100),
    district_old_ua       VARCHAR(100),
    district_new_ua       VARCHAR(100),
    district_new_en       VARCHAR(100),
    settlement_ua         VARCHAR(150),
    settlement_en         VARCHAR(150),
    post_office_ua        VARCHAR(255),
    post_office_en        VARCHAR(255),
    added_via_api         BOOLEAN   DEFAULT FALSE,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);