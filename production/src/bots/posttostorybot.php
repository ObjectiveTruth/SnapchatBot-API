<?php

require_once __DIR__ . "/masterbot.php";

/**
 * Posts all received snaps to story
 */
class PostToStoryBot extends MasterBot{

    protected function onNewFriendRequest($newFriend){
        echo "friend: $newFriend";
    }

    protected function onNewSnap($snap){
        echo "snapfile: " . $snap->full_path_to_snap_file;
    }
}

?>
