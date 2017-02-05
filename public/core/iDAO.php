<?php
    // This is DAO (Data Access Object).
    // It interfaces actual database module and service module.
    // Service module can use this interface to access database.
    
    interface iDAO
    {
        public static function getInstance(); // Returns singleton instance of the DAO.
        public function createDocument ($databaseStr, $collectionStr, $document); // Returns id of the document.
        public function retrieveDocument ($databaseStr, $collectionStr, $query); // Returns array of documents.
        public function updateDocument ($databaseStr, $collectionStr, $filter, $keyvaluepairs); // Returns update result.
        public function deleteDocument ($databaseStr, $collectionStr, $id); // Returns delete result. This function actually deletes equipment document from the DB.
                                               // Use updateEquipment function to mark the equipment as surplussed or trashed.
    }
?>