# Gir CMS

Gir CMS aims to be as lightweight and easy as possible while still maintaining a
certain amount of extensibility. It is named after the Invader Zim character
because of the authors obsessiveness with both the cartoon character and this
project.

While [HTML5 Boilerplate][html5boilerplate] is the basis for the template the
project is designed to be dynamic enough to (with a little effort) serve most
text based formats. You are also able to code in any standards you want.

## Requirements

* Apache 2.2+ w/mod_rewrite
* PHP 5+

## Installation

1. Upload the files, make sure the **cache** directory and **files/errors.log**
are writable.

2. Set up the **files/config.php** file.

3. Check/Change RewriteBase in the root **.htaccess** file.

4. Setup or delete the favicon(s) in **files/extras**.

5. Setup or delete the humans.txt and robots.txt in **files/extras**.

6. Go to the address you uploaded it to and it should now be working. Enjoy :)

## nginx support

Although I haven't tested it throughly you can get Gir-CMS working by adding the following files to the nginx.conf:

  location / {
      root   html/files/;
      index  index.php;
      
      if (!-e $request_filename) {
          rewrite  ^/(.*)$ index.php?url=$1  last;
          break;
      }
  }

[html5boilerplate]: http://html5boilerplate.com