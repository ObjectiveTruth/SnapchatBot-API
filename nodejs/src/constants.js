var constants = function(){}

module.exports = constants;

constants.createConstants = function(domainName){
    constants.SNAPS_NEED_APPROVAL_LIST_NAME = domainName + "-pending_approval";
    constants.SNAPS_AWAITING_POST_LIST_NAME = domainName + "-pending_post";
    constants.SNAPS_SAVE_DIRECTORY = __dirname + "/../../snaps/_" + domainName;
    constants.WEBM_TEMP_DIR = __dirname + "/../../webmtemp/_" + domainName;
}

constants.VIDEOFILE = 1;
constants.IMAGEFILE = 0;
constants.FRIEND_PERMISSION_CAN_POST = 0;
constants.FRIEND_PERMISSION_CANNOT_POST = 1;
constants.DEFAULT_FRIEND_PERMISSION = 0;
constants.WWWDIR = __dirname + "/../../www";
constants.WWWPUBLICDIR = __dirname + "/../../www/public";
constants.WWWPRIVATEDIR = __dirname + "/../../www/private";

