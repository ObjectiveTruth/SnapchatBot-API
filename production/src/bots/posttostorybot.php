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
        
        echo "friend: $newFriend\n";
    }

    protected function onNewSnap($snap){
        $this->markSnapIdAsViewed($snap->id, 2); 
    }
}

?>
