# self-hosted disposable email system

This disposable email solution can be hosted on your own standard PHP-webhoster. All you need is PHP with mailparse extension and "Pipe to a Program" functionality. The system is as simple as possible, with minimal codebase and complexity. 

 :warning: **Please note: there is also an simpler IMAP version without database and that does not need "pipe to command".** https://github.com/synox/disposable-mailbox

## Usage
When accessing the web-app a random email address is generated for you. The page will reload until emails have arrived. You can delete emails and see the original sourcecode. 

### Example Screenshot
![screenshot](assets/screenshot.png)

## Licence
Attribution-NonCommercial 4.0 International (CC BY-NC 4.0)

https://creativecommons.org/licenses/by-nc/4.0/

## Requirements

* PHP, Version 5.3.0
* Apache 2
* [mailparse extension](http://pecl.php.net/package/mailparse)
* [Composer](https://getcomposer.org/doc/00-intro.md#globally) (PHP Package Manager)

## Installation
- assure the mailparse extension is installed. The following command should not print any error: 
  
        <?php mailparse_msg_create(); ?>

- Clone/download this repository 
- run `composer install`

## Configuration
- forward/pipe email to the php script `app/pipe_input.php` (e.g.  [cpanel](https://documentation.cpanel.net/display/ALD/Forwarders#Forwarders-PipetoaProgram) docs)
- (optionally) configure a different database like mysql in `app/config.php`
- (optionally) configure the link redirection provider (to keep the existence of your installation secret) in `app/config.php`
 
## TODO
 1. security audit against xss/sqli

## development environment
There is a Vagrantfile to be used with [vagrant](https://www.vagrantup.com/). 

### OSX dependencies 
- install php: https://github.com/Homebrew/homebrew-php
- add php to path: fish config: `set PATH /usr/local/opt/php55/bin $PATH`
-  `pecl install mailparse`
- (see "php --ini" for file: ) `echo "extension=mailparse.so" >> /usr/local/etc/php/5.5/php.ini`

## Troubleshooting

### Mails do not appear in the mailbox
First make sure you check the php error log. also enable php error reporting with `error_reporting(E_ALL);` in `config.php`. 

Then also try to run the command manually from the command line. For this login into your server by ssh. 
Create a sample mail (like https://gist.github.com/synox/fa11060975bec7250a46) and save it somewhere on the server. 
Then run the script the same way as the mailserver would pipe the mail to it. 

        cat samplemail.txt | php /path/to/app/pipe_input.php

That should either process the mail or return an error. 

## I can't get it to work. It's too complicated. 
I have another solution which is easier to configure and install: https://github.com/synox/disposable-mailbox
 

## See also
 - inspired by script: https://github.com/moein7tl/TempMail/blob/master/web/index.php
     

