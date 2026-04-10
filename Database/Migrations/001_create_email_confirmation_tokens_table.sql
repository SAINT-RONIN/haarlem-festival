-- Email Confirmation Tokens Table
-- Stores temporary tokens for email verification when users change their email address
-- Tokens expire after 1 hour and are single-use

CREATE TABLE IF NOT EXISTS EmailConfirmationTokens (
    TokenId INT AUTO_INCREMENT PRIMARY KEY,
    UserAccountId INT NOT NULL,
    Email VARCHAR(255) NOT NULL,
    Token VARCHAR(64) NOT NULL UNIQUE,
    ExpiresAtUtc DATETIME NOT NULL,
    IsUsed TINYINT(1) NOT NULL DEFAULT 0,
    UsedAtUtc DATETIME NULL,
    CreatedAtUtc DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_email_confirmation_user
        FOREIGN KEY (UserAccountId) REFERENCES UserAccount(UserAccountId)
        ON DELETE CASCADE,

    INDEX idx_token (Token),
    INDEX idx_user_id (UserAccountId),
    INDEX idx_expires_at (ExpiresAtUtc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

