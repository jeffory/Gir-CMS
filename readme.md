# Gir CMS

Gir CMS aims to be as lightweight and easy as possible while still maintaining a
certain amount of extensibility.

While [HTML5 Boilerplate][html5boilerplate] is the basis for the template the
project is designed to be dynamic enough to (with a little effort) serve most
text based formats. You are also able to code in any standards you want.

## Requirements

* Apache 2.2+ w/mod_rewrite
* PHP 5.1+

## Installation

1. Upload the files, make sure the **cache** directory and **files/errors.log**
are writable.

2. Set up the **files/config.php** file.

3. Check/Change RewriteBase in the root **.htaccess** file.

4. Change or delete the favicon(s) in **files/extras**.

5. Change or delete the humans.txt and robots.txt in **files/extras**.

6. Go to the address you uploaded it to and it should now be working. Enjoy :)

## nginx support

Although it hasn't been tested throughly Gir-CMS seems to work on [nginx][nginx]
by adding the following lines to the **nginx.conf** file:

    location / {
        root   html/files/;
        index  index.php;
        
        if (!-e $request_filename) {
            rewrite  ^/(.*)$ index.php?url=$1  last;
            break;
        }
    }

[html5boilerplate]: http://html5boilerplate.com
[nginx]: http://nginx.org
