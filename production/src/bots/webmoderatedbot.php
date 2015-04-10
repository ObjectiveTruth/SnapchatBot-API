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
        
        $this->logger->addInfo("User Added: $newFriend");
    }

    protected function getDefaultFriendPermission(){
        return self::PERMISSION_CAN_POST;
    }

    protected function onNewSnap($snapObj){
        $snapFullPath = $snapObj->full_path_to_snap_file;
        $filenameOnly = basename($snapFullPath);

        $this->logger->addInfo("Received Snap: $filenameOnly\n");

        $this->markSnapIdAsViewed($snapObj->id, 2); 

        $this->addFilenameToPendingApprovalList($snapFullPath);
    }

    protected function onEndOfCycle(){
        $nextSnapToPost = $this->peekPendingPostSnap();

        while($nextSnapToPost != false){

            if($this->postSnapToStoryByFilename($nextSnapToPost) != false){

                $filenameOnly = basename($nextSnapToPost);
                $this->logger->addInfo("Posted to Story: $filenameOnly");
                $this->popPendingPostSnap();
            }
        }
        $t=time();
        echo("end of Cycle: " .date("Y-m-d_H:i:s",$t) . "\n");
    }
}

?>
