var Sequelize = require('sequelize');
var bcrypt = require('bcrypt-nodejs');
var sequelizeDomainSettings = new Sequelize(
        'snapchatbot_db', 'root', 'devtest', 
        {
            host: 'localhost',
            dialect: 'mysql',
            logging: false
        });

var sequelizeFriends; 


var orm = function(){
}

module.exports = orm;

orm.initialize = function(domainName){
    orm.domainName = domainName;

    sequelizeThisDomain = new Sequelize(
        domainName, domainName, 'ironhorse', 
        {
            host: 'localhost',
            dialect: 'mysql',
            logging: false
        });

    orm.friend = sequelizeThisDomain.define('friends', {
        userName: {
            type: Sequelize.STRING,
            field: 'username',
            primaryKey: true
        },
        permission:{
            type: Sequelize.INTEGER,
            field: 'permission'
        }
    }, {
        timestamps: false
    });

    orm.users = sequelizeThisDomain.define('users', {
        username: {
            type: Sequelize.STRING,
            field:'username',
            primaryKey: true
        },
        password: {
            type: Sequelize.STRING,
            field: 'password'
        },
        permission: {
            type: Sequelize.INTEGER,
            field: 'permission'
        },
        email: {
            type: Sequelize.STRING,
            field: 'email'
        },
        resetPasswordToken: {
            type: Sequelize.STRING,
            field: 'reset_password_token'
        },
        resetPasswordExpire: {
            type: Sequelize.DATE,
            field: 'reset_password_expire'
        }
    },{
        instanceMethods: {
            setPassword: function(password, done) {
                return bcrypt.genSalt(10, function(err, salt) {
                    return bcrypt.hash(password, 
                        salt, 
                        function(error, encrypted) {
                            this.password = encrypted;
                            this.salt = salt;
                            return done();}
            );});},
             comparePassword : function(candidatePassword, cb) {
                 bcrypt.compare(candidatePassword, 
                     this.getDataValue('password'), 
                     function(err, isMatch) {
                         if(err) return cb(err);
                         cb(null, isMatch);
                     });
             }
        },
        timestamps: false
    });
}


orm.domain = sequelizeDomainSettings.define('domains', {
    accountName: {
        type: Sequelize.STRING,
field: 'domainname',
primaryKey: true
    },
botType:{
    type: Sequelize.INTEGER,
field: 'bot_type'
}
}, {
    timestamps:false
});
