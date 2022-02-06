# gnekoz-framework
A homemade (abandoned) php MVC framework


This is a small MVC php framework that I made a while ago when after few years of coding in almost pure php I stumbled upon [Zend Framework](https://framework.zend.com/) so I tried to create something (remotely!) inspired by it. 
Obviously this is WAY different from Zend Framework and it's far from optimal, but it was fun to create, it gave me a chance to practice with a few design patterns, and I used it in a few projects before abandoning it and switching to a more reliable framework

### Main features
- MVC pattern
- Multiple strategies for the router (only one implemented)
- Various renderer for the view layer: HTML, JSON, plain text, binary file
- Integrated support for Smarty template engine
- Multiple language support (not completed)
- Application configuration via xml files. Configuration properties can be specified more than one under different profiles (eg. dev, test, producion, user, ...) and overriding the default values. The active profile can be set during the application bootstrap
- a CLI components generator (applications, controllers, actions)
