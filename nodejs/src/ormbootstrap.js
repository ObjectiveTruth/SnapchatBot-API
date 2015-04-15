var Sequelize = require('sequelize');
var sequelizeDomain = new Sequelize(
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

    sequelizeFriends = new Sequelize(
        domainName, domainName, 'ironhorse', 
        {
            host: 'localhost',
            dialect: 'mysql',
            logging: false
        });

    orm.friend = sequelizeFriends.define('friends', {
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
}


orm.domain = sequelizeDomain.define('domains', {
      accountName: {
          type: Sequelize.STRING,
          field: 'domainname',
          primaryKey: true
      },
      botType:{
          type: Sequelize.INTEGER,
          field: 'bot_type'
      },
      domainUsername: {
          type: Sequelize.STRING,
          field: 'domain_username'
      },
      domainPassword: {
          type: Sequelize.STRING,
          field: 'domain_password'
      }
}, {
    timestamps:false
});
