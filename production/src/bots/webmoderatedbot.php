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
        
        echo "User: $newFriend added me!\n";
    }

    protected function getDefaultFriendPermission(){
        return self::PERMISSION_CAN_POST;
    }

    protected function onNewSnap($snapObj){
        $snapFilename = $snapObj->full_path_to_snap_file;
        echo "Received Snap \n$snapFilename\n";

        $this->markSnapIdAsViewed($snapObj->id, 2); 

        $this->addFilenameToPendingApprovalList($snapFilename);
    }

    protected function onEndOfCycle(){
        $nextSnapToPost = $this->peekPendingPostSnap();
        while($nextSnapToPost != false){
            if($this->postSnapToStoryByFilename($nextSnapToPost) != false){
                $this->popPendingPostSnap();
            }
        }
        $t=time();
        echo("end of Cycle: " .date("Y-m-d_H:i:s",$t) . "\n");
    }
}

?>
