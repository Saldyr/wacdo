ALTER TABLE `utilisateur`
    ADD COLUMN `consentement` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN `date_consentement` DATETIME NULL;
