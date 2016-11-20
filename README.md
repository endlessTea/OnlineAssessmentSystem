# OnlineAssessmentSystem

## Summary

## Set-up

*Note: guidance was written in August 2015 and may be deprecated. Exact steps required may be slightly different*

To run the project you will need the following resources:
* A PHP development environment / Apache server (XAMPP or a LAMP stack).
* MongoDB.
* PHP Driver for MongoDB.
* Configured Path Environment Variables to run MongoDB from Command Prompt (Windows users only). This may automatically be configured for Linux users.
* Imported sample data from user evaluations (optional).

### Step 1: Download and install XAMPP

Go to [the Apache website](https://www.apachefriends.org/index.html) and download the version of XAMPP for your operating system. Run the executable once it has been downloaded. This should come with version 5.6.11 of PHP.
You may be required to temporarily disable any Antivirus software, as this could interfere with the installation. You may also receive a warning advising that if User Access Control (UAC) is activated, you should avoid installing the program to the ‘Program Files’ folder. It is recommended to install it to C:/xampp instead.
You will then need to select the components to install. You only need to install the Apache Server and PHP; all of the other features do not need to be installed. Click ‘Next’, and de-select the box for ‘Learn more about Bitnami’ (it’s not required). Click ‘Next’ twice to begin the installation.
Platform-specific FAQs can be found for [Windows](https://www.apachefriends.org/faq_windows.html), [Mac](https://www.apachefriends.org/faq_osx.html) and [Linux](https://www.apachefriends.org/faq_linux.html).

### Step 2: Download and install MongoDB

Visit [MongoDB](https://www.mongodb.org/downloads) and download the version of MongoDB for your operating system. Run the executable once it has been downloaded. Take note of the version of MongoDB you are installing! This is likely to be version 3+. Take note of the folder (directory) that MongoDB is installed to, you will need this location later.
Click ‘Next’ and agree to the GNU licence agreement. Choose the ‘Complete’ installation and click ‘Finish’ once completed.

### Step 3: Download and transfer MongoDB PHP driver

[Download the MongoDB PHP driver](http://docs.mongodb.org/ecosystem/drivers/php/) and follow instructions to install the PHP Driver for MongoDB. You should download version 1.6.x of the PHP driver if you have installed MongoDB version 3+. Download the appropriate ZIP archive from Amazon Web Services (Windows users). Extract the archive and copy the file for your 32 or 64 bit operating system:

```
php_mongo-1.6.8-5.6-vc11.dll 
php_mongo-1.6.8-5.6-vc11-x86_64.dll
```

To the following directory (or the appropriate directory for Linux users):

```
C:\xampp\php\ext
```

Rename the file to:

```
php_mongo.dll
```

Open the XAMPP Control Panel, click ‘Config’, then ‘PHP (php.ini) and add the following to your php.ini file, remembering to save and close the file:

```
extension=php_mongo.dll
```

### Step 4: Update PATH Environment variables

This may be configured by default for Linux users, although Windows users will need to complete this step. Go to [PHP's path guidance](http://us3.php.net/manual/en/faq.installation.php#faq.installation.addtopath) and follow the instructions up to the point where you are about to press ‘OK’. You may need to change icons to ‘Small icons’, click ‘System’, then ‘Advanced System Settings’, then ‘Environment Variables’. You need to change the PATH variable in ‘System variables’, not ‘User variables’. Be careful not to delete the text already associated with the PATH environment variable! Follow the instructions carefully, add a semi-colon and the path to your PHP folder in XAMPP to the end:

```
C:\xampp\php
```

Now, without clicking ‘OK’ open Windows Explorer and navigate to the directory where MongoDB is installed. Double click ‘Server’, then ‘3.0’, then ‘bin’, then right click on ‘mongo’ and copy the Location text.

Return to the ‘Edit System Variable’ window and APPEND a semi-colon to the variable value, then paste the Location text after it. NOW click ‘OK’, then ‘OK’ again.
