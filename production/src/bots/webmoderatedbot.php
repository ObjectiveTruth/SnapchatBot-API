<?php

require_once __DIR__ . "/masterbot.php";
require_once __DIR__ . "/../ormbootstrap.php";

/**
 * Posts all received snaps to story
 */
class WebModeratedBot extends MasterBot{

    protected function onNewFriendRequest($newFriend){
        $this->saveFriendByNameToDBWithDefaults($newFriend);
        $this->addFriendByName($newFriend);
        
        echo "friend: $newFriend\n";
    }

    protected function getDefaultFriendPermission(){
        return $this->PERMISSION_CAN_POST;
    }

    protected function onNewSnap($snapObj){
        $snapFilename = $snapObj->full_path_to_snap_file;

        $this->markSnapIdAsViewed($snapObj->id, 2); 

        $this->addFilenameToPendingApprovalList($snapFilename);
    }

    protected function onEndOfCycle(){
    }
}

?>
