<?php
echo password_hash('test', PASSWORD_DEFAULT);
//$2y$12$lAzqKJ1iP/I.G5JUcKW92usDW19REkgn4DimtMjcnowCG/bjovW22
echo 'PREP:    ' . password_hash('test1',   PASSWORD_BCRYPT) . "\n";
echo 'ACCUEIL: ' . password_hash('test2',PASSWORD_BCRYPT) . "\n";