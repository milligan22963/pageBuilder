# pageBuilder
===========

A site building repository based on PHP/HTML/CSS/JS

To get started, install on your favorite server and edit the configxml.php file under the "configure" directory.  The main items are the database to use and db username/pwd.  I originally was using config.xml however due to it being a security risk, I opted to make it a php source and generate the xml via heredoc and display a note to someone trying to load it directly.  Once that is in place, open up the <your site>/install/install.php to configure the system.  Create all of the tables then load the "index.php" file at the top level.  Your site should be live.

NOTE: Currently only supporting MySQL, Postgres is in progress.

Admin user is "Admin" and default password is "default".

thanks,

-Dan
