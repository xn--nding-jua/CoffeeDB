# CoffeeDB
An interactive Coffee-Logging- and Invoice-System with eMail-notification for small offices

![image](https://user-images.githubusercontent.com/9845353/136352339-f076193f-829f-4f4c-a962-8c3f71fc2b91.png)

# Overview
This piece of software offers a nice interactive Coffee-Database for small offices with 5 to 20 persons. It is designed to replace common paper-based checklists and allows basic invoices at the end of each month. Additionally each user can check his or her dayly coffee-consumption and drink even more if it wasn't enough :)

The integrated Cash-Invoice-System helps you to manage the Coffee-Cash-Register
![image](https://user-images.githubusercontent.com/9845353/136352787-43df65f8-87ce-4cf9-9266-64813e054359.png)

# Requirements
- PHP 7.x
- cronjobs for sending monthly eMails

# Installation
1. Change to the directory of your webserver and checkout the code
- cd /var/www/html
- git clone https://github.com/xn--nding-jua/CoffeeDB.git

2. Download all nescesary components by starting install.sh-script
- cd CoffeeDB
- bash install.sh

3. Check if the components-directory now contains four subfolders:
- bootstrap
- jpgraph
- MaterialDesign-Webfont
- PHPMailer

4. Create a new cronjob for sending mails (keep an eye on the user-rights for backup DB and sending mails)
- crontab -e
- 0 0 1 * * php /var/www/html/CoffeeDB/CoffeeSendMails.php

5. Open browser and browse to index.php or Cash.php
- https://www.yourdomain.de/CoffeeDB/index.php -> User-Frontend
- https://www.yourdomain.de/CoffeeDB/Cash.php -> Management-Backend


# Example for User-eMail
Users will receive an eMail sent by cronjob containing the current number of coffees and the bank balance:
![image](https://user-images.githubusercontent.com/9845353/136380417-44cd4ee5-8bfb-49eb-910e-720ccfdf5e47.png)
