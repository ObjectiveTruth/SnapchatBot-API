# Snap API

Interface to Snapchat

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

    ```php composer.phar install``` then reboot for lock update before 

    ```php composer.phar update```

    
### Special thanks

- [teknogeek](https://github.com/teknogeek)
- [JorgenPhi](https://github.com/JorgenPhi)
- [hako](https://github.com/hako)
- [0xTryCatch](https://github.com/0xTryCatch)
- [kyleboyer](https://github.com/kyleboyer)

Based on [JorgenPhi](https://github.com/JorgenPhi/php-snapchat) code.

## License
MIT
