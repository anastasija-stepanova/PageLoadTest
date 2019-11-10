<?php

require_once __DIR__ .'/migration_utils.php';

$conn = connectDB();
$files = getMigrationFiles($conn);

if (empty($files))
{
    echo 'Ваша база данных в актуальном состоянии.';
}
else
{
    echo 'Начинаем миграцию...<br><br>';

    foreach ($files as $file)
    {
        migrate($conn, $file);
        echo basename($file) . '<br>';
    }

    echo '<br>Миграция завершена.';
}