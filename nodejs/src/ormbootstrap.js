var Sequelize = require('sequelize');
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
        }
    },{
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
