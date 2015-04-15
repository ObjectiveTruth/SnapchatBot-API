var Sequelize = require('sequelize');
var sequelizeDomain = new Sequelize(
        'snapchatbot_db', 'root', 'devtest', 
        {
            host: 'localhost',
            dialect: 'mysql'
        });

var sequelizeFriends; 


var orm = function(){
}

module.exports = orm;

orm.initialize = function(domainName){
    orm.domainName = domainName;

    sequelizeFriends = new Sequelize(
        'domainName', 'root', 'devtest', 
        {
            host: 'localhost',
            dialect: 'mysql'
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


orm.customer = sequelizeDomain.define('domains', {
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
