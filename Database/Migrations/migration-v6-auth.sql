-- =====================================================
-- Migration v6: Update UserAccount for Argon2id password hashing
-- Run after migration-v5.sql
-- =====================================================

-- Argon2id hashes are ~95-100 characters, VARBINARY(64) is too small.
-- Change PasswordHash to VARCHAR(255) to safely store Argon2id output.
-- Make PasswordSalt nullable since Argon2id embeds salt in the hash.

ALTER TABLE `UserAccount`
  MODIFY `PasswordHash` VARCHAR(255) NOT NULL,
  MODIFY `PasswordSalt` VARBINARY(32) NULL;

