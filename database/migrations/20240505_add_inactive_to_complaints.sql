-- Add is_inactive column to complaints table
ALTER TABLE complaints
ADD COLUMN is_inactive BOOLEAN DEFAULT FALSE,
ADD COLUMN inactive_reason VARCHAR(255) DEFAULT NULL,
ADD COLUMN inactivated_at DATETIME DEFAULT NULL;
