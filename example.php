<?php

try {
    $newDb->beginTransaction();
    // *Start Execution

    $rows = $oldDb->fetchAll("SELECT 'lang_group' FROM core_translations LIMIT 10");

    // *End Execution

    $newDb->commit();
    echo "Data migrated successfully.\n";
} catch (Exception $e) {
    // !Rollback the transaction in case of an error
    $newDb->rollBack();
    echo "Failed to migrate data: " . $e->getMessage();
}
