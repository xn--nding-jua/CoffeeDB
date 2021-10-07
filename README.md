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
1. Create a directory in scope of your webserver and checkout the code
- cd /var/www/html
- mkdir Coffee
- git clone https://github.com/xn--nding-jua/CoffeeDB.git

2. Create a new cronjob for sending mails
- crontab -e
- 0 0 1 * * php /var/www/html/Coffee/CoffeeSendMails.php

3. Open browser and browse to Coffee.php or Cash.php
- https://www.yourdomain.de/Coffee/Coffee.php
- https://www.yourdomain.de/Coffee/Cash.php


# Used softwares
- jpgraph
- PHPMailer
- bootstrap
- jquery
