
//this require at the very top to check cmd line
var program = require('./src/commandline.js');
var utils = require('./src/utils.js');

var express = require('express');
var app = express();
var bodyParser = require('body-parser');
var winston = require('winston');
var expressSession = require('express-session');
var redisStore = require('connect-redis')(expressSession);
var flash = require('connect-flash');
var async = require('async');
var crypto = require('crypto');
var nodemailer = require('nodemailer');
var sgTransport = require('nodemailer-sendgrid-transport');
var path = require('path');
var cookieParser = require('cookie-parser');
var fs = require('fs');

var mailer = nodemailer.createTransport(sgTransport(
            {auth: 
                {api_user: 'user_key', 
                    api_key: 'user_password'}
            }));


var constants = require('./src/constants');
constants.createConstants(program.domainName);

var dbORM = require('./src/ormbootstrap.js');
dbORM.initialize(program.domainName);

var redis = require('redis'),
    redisClient = redis.createClient();

var passport = require('passport'),
    LocalStrategy = require('passport-local').Strategy;

passport.use(new LocalStrategy({
        passReqToCallback: true
    },
    function(req, username, password, done) {
        dbORM.users.find(username).then(function(user) {
            if (user == null) {
                winston.info('User Not Found: ' + username);
                return done(null, false, 
                    req.flash('message', 'Incorrect username.'));
            }
            if (user.password != password) {
                winston.info('Invalid Password for User: ' + username);
                return done(null, false, 
                    req.flash('message', 'Incorrect password.'));
            }
            return done(null, user);
        });
    }
));
passport.serializeUser(function(user, done) {
    done(null, user);
});

passport.deserializeUser(function(user, done) {
    done(null, user);
});



//AutoParses responses into json and gets cookies
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended:true}));
app.use(cookieParser());
app.use(expressSession({ 

    name: program.domainName,
    resave: false,
    saveUninitialized: false,
    store: new redisStore({
        host: 'localhost',
    port: 6379,
    client: redisClient
    }),
    secret: 'somecrazyhash' 

}));
app.use(flash());
app.use(passport.initialize());
app.use(passport.session());

//Server the saved snap pics statically
app.use(express.static(constants.WWWPUBLICDIR));

app.get('/login', function(req, res){
    res.sendFile(path.resolve(constants.WWWPUBLICDIR + "/login.html"));
});

app.post('/login', function(req, res, next){
    passport.authenticate('local', function(err, user, info) {
        if (err) return next(err)
        if (!user) {
            return res.json({message: "Username/Password combination not found"});
        }
    req.logIn(user, function(err) {
        if (err) return next(err);
        return res.json({redirect: "/"});
    });
    })(req, res, next);
});


app.get('/logout', utils.loggedIn, function(req, res){
    req.logout();
    res.redirect('/');
});

app.get('/reset/:token', function(req, res) {
    dbORM.users.find({ where: 
        { resetPasswordToken: req.params.token, 
            resetPasswordExpires: { $gt: new Date() } }})
        .then(function(user) {
            if (user == null) {
                winston.warn("Couldn't find user with a valid Password Reset Token");
                return res.redirect('/forgot');
            }else{
                winston.info("Password Reset Token is valid, redirecting..");
                res.redirect("/passchanger.html?token=" + req.params.token);
            }});
});

app.post('/reset/:token', function(req, res) {
    async.waterfall([
        function(done) {
            dbORM.users.find({ where:
                {resetPasswordToken: req.params.token, 
                    resetPasswordExpires: { $gt: new Date() } }})
                .then(function(user) {
                if (user == null) {
                    winston.warn("Password token was not found or is expired");
                    return res.json({type: "fail", 
                        message: "Password reset token is invalid or has expired"});
                }

                user.password = req.body.password1;
                user.resetPasswordToken = undefined;
                user.resetPasswordExpires = undefined;

                user.save().then(function() {
                    winston.verbose("Successfully changed password in db");
                    req.logIn(user, function(err){
                        done(err, user);
                    });
                }).catch(function(err){
                    winston.err("Couldn't store password reset token and expiry to db");
                });
            });
        },
        function(user, done) {
            var email = {
                to: user.email,
                from: 'passwordreset@snapify.com',
                subject: 'Your password has been changed',
                text: 'Hello,\n\n' +
                    'This is a confirmation that the password for your account ' + user.email + ' has just been changed.\n'
            };
            mailer.sendMail(email, function(err, res){
                if(err){
                    winston.error("Couldn't send password confirm change " +
                        "email through SendGrid API: ");
                }else{
                    winston.verbose("Confirmed Pass Change Email Sent! Response:");
                }
                done(err, 'done');
            });

        }
    ], function(err) {
        if(err){
            winston.error("Failed to change password");
            res.json({type: "fail", 
                message: "Could not change password, if this persist contact us"});
        }else{
            winston.info("Successfully changed password");
            res.json({redirect: "/"});
        }
    });
});

app.post('/forgot', function(req, res) {
    winston.info("Received forgot request");
    async.waterfall([
        function(done) {
            crypto.randomBytes(20, function(err, buf) {
                var token = buf.toString('hex');
                done(err, token);
            });
        },
        function(token, done) {
            dbORM.users.find({ where: {email :req.body.email} }).then(function(user) {
                if (user == null) {
                    winston.warn("Attempted to reset password, but email not found: " + 
                        req.body.email);
                    return res.json({message: "No account with that email exists"});
                }

                var currentDate = new Date();

                user.resetPasswordToken = token;
                user.resetPasswordExpires = 
                currentDate.setHours(currentDate.getHours() + 1);

            user.save().then(function() {
                done(null, token, user.email);
            }).catch(function(err){
                winston.err("Couldn't store password reset token and expiry to db");
            });
            });
        },
        function(token, userEmail, done) {
            var email = {
                to: userEmail,
                from: 'passwordreset@snapify.ca',
                subject: 'Snapify Password Reset',
                text: 'You are receiving this because you (or someone else) have requested the reset of the password for your account.\n\n' +
                    'Please click on the following link, or paste this into your browser to complete the process:\n\n' +
                    'http://' + req.headers.host + '/reset/' + token + '\n\n' +
                    'If you did not request this, please ignore this email and your password will remain unchanged.\n'
            };
            mailer.sendMail(email, function(err, res){
                if(err){
                    winston.error("Couldn't send email through SendGrid API: ");
                }else{
                    winston.verbose("Email Sent! Response: " + res);
                }
                done(err, 'done');
            });
        }
    ], function(err) {
        if (err) {
            winston.error("Error occured while creating/sending password reset token");
            return res.json({type: "fail",
                message: "There was an error generating token"});
        }else{
            winston.info("Sent reset password email");
            res.json({type: "success", 
                message: "A password reset email has been sent. Check your spam"});
        }
    });
});

app.use(utils.loggedIn);
////////////////////////
////////////////////////
//BELOW THIS IS PRIVATE
////////////////////////
////////////////////////

app.get('/', function(req, res, next){
    res.sendFile(path.resolve(constants.WWWPRIVATEDIR + "/home.html"));
});




//Gets the next snap to be evaluated
app.get('/getnext', function (req, response){
    var REDIS_TAIL_INDEX = -1;
    winston.info("Received getnext request");
    //create empty JSObject to be filled before sending
    var returnJSONObject = {};
    //Get the remaining snaps that need approval starting with the tail(0, -1)
    redisClient.lindex(constants.SNAPS_NEED_APPROVAL_LIST_NAME, 
        REDIS_TAIL_INDEX, 
        function(error, reply){
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
                permissionCode = constants.DEFAULT_FRIEND_PERMISSION;
                var mediaType = constants.IMAGEFILE;
                if(utils.isVideo(filename)){mediaType = constants.VIDEOFILE;}
                //Fill the object with the corresponding entries
                returnJSONObject["username"] = username;
                returnJSONObject["filename"] = filename;;
                returnJSONObject["type"] = mediaType;
                returnJSONObject["identifier"] = identifier;
                returnJSONObject["permissionCode"] = permissionCode;
            }
            winston.verbose("Returned getnext", returnJSONObject);
            //Return the JSON Object Client will check for null
            response.json(returnJSONObject);
        });

});

app.get('/getwebmedia/:filename', function(req, res){
    var filename = req.params.filename;
    var filenameFullPath;

    if(utils.isVideo(filename)){
        filenameFullPath = path.join(
            constants.WEBM_TEMP_DIR,
            path.basename(filename, '.mp4') + ".webm");
    }else if(utils.isImage(filename)){
        filenameFullPath = path.join(
            constants.SNAPS_SAVE_DIRECTORY,
            filename);
    }else{
        res.sendStatus(400);
    }
    fs.exists(filenameFullPath, function(exists) {
        if (exists) {
            winston.info("Sending: " + filename);
            res.sendFile(filenameFullPath);
        } else {
            winston.warn("No such filename: " + filenameFullPath);
            res.sendStatus(400);
        }
    });
});

//Bans a user by placing the appropriate permission on Redis
app.post('/banuser', function(request, response){
    winston.info("Received banuser request for user: " + request.body.username);
    //Sets the redit entry for that user to CANNOT POST
    //TODO NEEDS TO BE REDONE
    response.sendStatus(200);
});

//Attempts to pop the element sent from /getnext
app.post('/popnext/:isApproved', function(request, response){
    var REDIS_HEAD_INDEX = 0;
    winston.info("Received popnext request");
    redisClient.lrem(constants.SNAPS_NEED_APPROVAL_LIST_NAME, 
        REDIS_HEAD_INDEX, request.body.identifier, function(error, reply){
            //Reply = how many were removed. > 0 means there was something to remove
            if(reply > 0){
                winston.verbose("Identifier: " + request.body.identifier + 
                    ", Status: Removed from AWAITING_APPROVAL_LIST");
                //if :isApproved is true, then add to the Awaiitng post queue
                if(request.params.isApproved == "true"){
                    winston.info("Username: " + request.body.username + 
                        ", Approved: True");
                    redisClient.lpush(constants.SNAPS_AWAITING_POST_LIST_NAME, 
                        request.body.identifier, function(error, reply){
                            if(reply > 0){
                                winston.verbose("Identifier: " + 
                                    request.body.identifier + 
                                    ", Message: pushed to AWAITING_POST_LIST");
                            }else{
                                winston.error("Identifier: " + 
                                    request.body.identifier + 
                                    ", Message: Coudln't push identifier to AWAITING_POST_LIST");
                            }
                        });
                }else{
                    winston.info("Username: " + 
                            request.body.username + ", Approved: False");
                    winston.verbose("Identifier: " + 
                            request.body.identifier + ", Status: discarded..");
                }
                //Everything worked out, send OK and move on
                response.sendStatus(200);
            }else{
                winston.error("Key: " + request.body.identifier + 
                        ", Status: Didn't exist, sending 400 Malrequest code back");
                //Reply = how many removed. Since <= 0, we know it didn't exist, 
                //send NO OK signal
                response.sendStatus(400);
            }

        });

});


app.use(express.static(constants.WWWPRIVATEDIR));

app.get('/*', function(req, res, next){
           res.redirect('/');
           });

           var server = app.listen(program.portNumber, 
           program.acceptConnectionsFrom, function () {

           var host = server.address().address
           var port = server.address().port

           winston.info('moderator app for ' + program.domainName + 
           " listening at http:" + host + ":" +  port);

           });

