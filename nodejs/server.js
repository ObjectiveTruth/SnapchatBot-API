require('newrelic');
var express = require('express');
var app = express();
var bodyParser = require('body-parser');
var winston = require('winston'),
    WinstonNewrelic = require('winston-newrelic');

//Automatically adds a log transport to console

//Constants
var REDIS_CATEGORY_USERNAME = "USERNAME:";
var REDIS_KEY_SNAPS_NEED_APPROVAL_LIST = "SNAPS_NEED_APPROVAL";
var REDIS_KEY_SNAPS_AWAITING_POST_LIST = "SNAPS_AWAITING_POST";
var SNAPS_SAVE_DIRECTORY = __dirname + "/SnapchatPics";
var REDIS_FRIEND_CAN_POST = '1';
var REDIS_FRIEND_CANNOT_POST = '0';

//Default permission, used for getting new users, if not found, what do?
var DEFAULT_FRIEND_PERMISSION = REDIS_FRIEND_CAN_POST;

//Server the saved snap pics statically
app.use(express.static(SNAPS_SAVE_DIRECTORY));
app.use(express.static(__dirname + '/www'));


//AutoParses responses into json
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

//Create the db connection to Reddis. Default is db 0
var redis = require('redis'),
    client = redis.createClient();

//Gets the next snap to be evaluated
app.get('/getnext', function (req, response){
    winston.info("Received getnext request");
    //create empty JSObject to be filled before sending
    var returnJSONObject = {};
    //Get the remaining snaps that need approval starting with the tail(0, -1)
    client.lindex(REDIS_KEY_SNAPS_NEED_APPROVAL_LIST, -1, function(error, reply){
        //If there are any snaps that need reviewing
        if (reply != null){
            //Full Identifier/path
            var identifier = reply;
            //Gets the portion after the last '/'
            var filename = reply.split('/').pop();
            //Regex to find the position after username _99999r.jpg
            var endOfName = filename.lastIndexOf("_");
            winston.verbose("endOfName: " + endOfName);
            //Uses the position to cut the filename to JUST the username
            var username = filename.substring(0, endOfName);
            //Get that user's permission
            var permissionCode = client.get(REDIS_CATEGORY_USERNAME + username);
            if(permissionCode == null){
                permissionCode = DEFAULT_FRIEND_PERMISSION;
            }
            //Fill the object with the corresponding entries
            returnJSONObject["username"] = username;
            returnJSONObject["filename"] = filename;;
            returnJSONObject["identifier"] = reply;;
            returnJSONObject["permissionCode"] = permissionCode;
        }
        winston.verbose("Returned getnext", returnJSONObject);
        //Return the JSON Object Client will check to see if there's anything in it
        response.json(returnJSONObject);
    });

});

//Bans a user by placing the appropriate permission on Redis
app.post('/banuser', function(request, response){
    winston.info("Received banuser request for user: " + request.body.username);
    //Sets the redit entry for that user to CANNOT POST
    client.set(REDIS_CATEGORY_USERNAME + request.body.username, REDIS_FRIEND_CANNOT_POST);
    response.sendStatus(200);
});

//Attempts to pop the element sent from /getnext
app.post('/popnext/:isApproved', function(request, response){
    winston.info("Received popnext request");
    client.lrem(REDIS_KEY_SNAPS_NEED_APPROVAL_LIST, 0, request.body.identifier, function(error, reply){
        //Reply = how many were removed. > 0 means there was something to remove
        if(reply > 0){
            winston.verbose("Identifier: " + request.body.identifier + ", Status: Removed from AWAITING_APPROVAL_LIST");
            //if :isApproved is true, then add to the Awaiitng post queue
            if(request.params.isApproved == "true"){
                winston.info("Username: " + request.body.username + ", Approved: True");
                client.lpush(REDIS_KEY_SNAPS_AWAITING_POST_LIST, request.body.identifier, function(error, reply){
                    if(reply > 0){
                        winston.verbose("Identifier: " + request.body.identifier + ", Message: pushed to AWAITING_POST_LIST");
                    }else{
                        winston.error("Identifier: " + request.body.identifier + ", Message: Coudln't push identifier to AWAITING_POST_LIST");
                    }
                });
            }else{
                winston.info("Username: " + request.body.username + ", Approved: False");
                winston.verbose("Identifier: " + request.body.identifier + ", Status: discarded..");
            }
            //Everything worked out, send OK and move on
            response.sendStatus(200);
        }else{
            winston.error("Key: " + request.body.identifier + ", Status: Didn't exist, sending 400 Malrequest code back");
            //Reply = how many removed. Since <= 0, we know it didn't exist, send NO OK signal
            response.sendStatus(400);
        }

    });

});

var server = app.listen(5000, '127.0.0.1', function () {

      var host = server.address().address
      var port = server.address().port

      winston.info('moderator app listening at http:' + host + ":" +  port)

})

