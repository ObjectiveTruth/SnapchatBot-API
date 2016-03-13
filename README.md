# Snap API [Deprecated]

## Important :bangbang:

No longer maintained and not recommended for usage. Snapchat has since changed their service significantly and thus my fork has been abandoned. This is for reference only

## What was it suppose to do?

The idea was to create an automated bot that would do respond to remote commands from a webpage to manage one's Snapchat account

## Technologies breakdown

The Snapchat Bot itself was built in **PHP** and is a fork of another project. 

Tests were added using **PHPUnit**

Backend was build in **Nodejs** using the **ExpressJS** framework.
Authentication was managed using **PassportJS** with state being handled by **Redis**
User's information was managed using a **MySQL** database
Commandline Utility was created for Administrative tasks using **CommanderJS**

--------

**Read the [wiki](https://github.com/mgp25/Snap-API/wiki)** and previous issues before opening a new one! Maybe your issue is already answered.

----------

##ObjectiveTruth Setup

* Install php5-cli

    ```sudo apt-get install php5-cli php5-mcrypt php5-gd```

* Install mysql

    ```sudo apt-get install mysql-server php5-mysql```
    
    Set the password(importat), before install

    ```sudo mysql_install_db```

    Secure Setup(no, yes, yes, yes, yes)

    ```sudo /usr/bin/mysql_secure_install```

* Install composer (php dependancy manger)

    Instructions from [composer](https://getcomposer.org/doc/00-intro.md)

    Install to local directory ```curl -sS https://getcomposer.org/installer | php```

    ```php composer.phar install``` then ```php composer.phar update```

    For Development install [phpunit](https://phpunit.de/getting-started.html)

    ```php5 phpunit.phar ./``` to run all tests

* Install Redis

    Follow Directions: ```http://redis.io/topics/quickstart```

    Make sure to add ```bind 127.0.0.1``` to only allow local traffic

* Install Ngnix

    Ngnix Server auto-starts after install finishes on port 80

    ```sudo -s```

    nginx=stable # use nginx=development for latest development version

    ```add-apt-repository ppa:nginx/$nginx```

    apt-get update 

    ```apt-get install nginx```


###Notes

* DO NOT Install PHPunit from apt-get

* ```php vendor/bin/doctrine``` for Doctrine Helper Tools

    
### Special thanks

- [teknogeek](https://github.com/teknogeek)
- [JorgenPhi](https://github.com/JorgenPhi)
- [hako](https://github.com/hako)
- [0xTryCatch](https://github.com/0xTryCatch)
- [kyleboyer](https://github.com/kyleboyer)

Based on [JorgenPhi](https://github.com/JorgenPhi/php-snapchat) code.

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/mgp25/Snap-API?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## License
MIT
