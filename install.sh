#!/bin/bash
#
# This scripts downloads the following components to the named directories:
# Component               | Foldername             | Website
# ========================|========================|========================================================
# MaterialDesign-Webfont  | MaterialDesign-Webfont | https://github.com/Templarian/MaterialDesign-Webfont
# Bootstrap-Theme         | bootstrap              | https://github.com/thomaspark/bootswatch/tree/v5
# jpgraph                 | jpgraph                | https://jpgraph.net/download/
# PHPMailer               | PHPMailer              | https://github.com/PHPMailer/PHPMailer
#
#

mkdir DB_Backups
mkdir components
cd components

echo "Downloading MaterialDesign-Webfont..."
wget https://github.com/Templarian/MaterialDesign-Webfont/archive/refs/tags/v6.2.95.zip -O materialdesignwebfont.zip
unzip materialdesignwebfont.zip
rm materialdesignwebfont.zip
find . -name "MaterialDesign-Webfont*" -exec mv '{}' MaterialDesign-Webfont/ \;&> /dev/null

echo "Downloading Bootstrap-Theme..."
curl -s https://api.github.com/repos/twbs/bootstrap/releases/latest | grep "browser_download_url.*dist.zip" | cut -d : -f 2,3 | tr -d \" | wget -qi - -O bootstrap.zip
unzip bootstrap.zip
rm bootstrap.zip
find . -name "bootstrap*" -exec mv '{}' bootstrap/ \;&> /dev/null
wget https://raw.githubusercontent.com/thomaspark/bootswatch/v5/dist/darkly/bootstrap.min.css -O bootstrap/css/bootstrap.darkly.css

echo "Downloading jpgraph..."
wget https://jpgraph.net/download/download.php?p=49 -O jpgraph.tar.gz
tar -xzvf jpgraph.tar.gz
rm jpgraph.tar.gz
find . -name "jpgraph*" -exec mv '{}' jpgraph/ \;&> /dev/null

echo "Downloading PHPMailer..."
curl -s https://api.github.com/repos/PHPMailer/PHPMailer/releases/latest | grep "tarball_url" | cut -d : -f 2,3 | sed 's/.$//' | tr -d \" | wget -qi - -O phpmailer.tar.gz
tar -xzvf phpmailer.tar.gz
rm phpmailer.tar.gz
find . -name "PHPMailer-*" -exec mv '{}' PHPMailer/ \;&> /dev/null

