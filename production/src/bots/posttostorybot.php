<?php

require_once __DIR__ . "/masterbot.php";
require_once __DIR__ . "/../ormbootstrap.php";

/**
 * Posts all received snaps to story
 */
class PostToStoryBot extends MasterBot{

    protected function onNewFriendRequest($newFriend){
        $this->saveFriendByNameToDBWithDefaults($newFriend);
        $this->addFriendByName($newFriend);
        
        $this->logger->addInfo("User Added: $newFriend");
    }

    protected function getDefaultFriendPermission(){
        return $this->PERMISSION_CAN_POST;
    }

    protected function onNewSnap($snapObj){
        $this->markSnapIdAsViewed($snapObj->id, 2); 
        $snapFullPath = $snapObj->full_path_to_snap_file;
        if($this->postSnapToStoryByFilename($snapFullPath) != false){
            $filenameOnly = basename($snapFullPath);
            $this->logger->addInfo("Posted to Story: $filenameOnly");
        } 
    }
}

?>
