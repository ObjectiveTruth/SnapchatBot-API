var path = require('path');

exports.loggedIn = function(req, res, next){
    if (req.user) {
        next();
    } else {
        res.redirect('/login');
    }
};

exports.isVideo = function(filename){
    return path.extname(filename) == '.mp4';
};

exports.isImage = function(filename){
    return path.extname(filename) == '.jpg';
};

