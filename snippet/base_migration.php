<?php

try {
    $newDb->beginTransaction();
    // *Start Execution
    
    // Code...
    
    // *End Execution

    $newDb->commit();
    echo "Data migrated successfully.\n";
} catch (Exception $e) {
    // !Rollback the transaction in case of an error
    $newDb->rollBack();
    echo "Failed to migrate data: " . $e->getMessage();
}
