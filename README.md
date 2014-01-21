Qlusterfuq
==========

This is the Qlusterfuq discussion group system. It's intended to be a simple, easy-to-deploy alternative to a Facebook group.

##How to set up Qlusterfuq

Eventually, we'll have a fancy install script that does all this for you. For now...

1. Open `data.php` and set the `DB_ROOT` function to return the root path to your server. This should be an absolute path.

2. You'll need to go into the `db/set` folder and change the following:

- `admincontact` - Set this to an email address
- `daemon` - Set this to a no-reply email address
- `sitename` - Set this to the name of your site
- `systemroot` - Set this to the root URL of your Qlusterfuq site
- `regdesc` and `regintro` - Set these to change what appears on the "Apply to Join" page

3. Create an email address on your mail server to send outgoing mail. Then, in `db/set`:

- `mailhost` - The URL of your mail server
- `smtp-mailer` - The login for the email address
- `smtp-password` - The password for the email address
- `smtp-port` - The SMTP port

4. For faster performance, set `cronemail` to "true" and then configure your server to run cron.php as often as possible. This isn't required, though.

5. To create your first user, go to Apply to Join and go through the process. Your `admincontact` email will receive the join request. Approve it. To make this new user an administrator, go to the `db/u/(User's ID)` folder and create a file called `admin`. Make the text of this file `yes`.

6. For security, chmod the folder permissions of `db` to 700. You can also move the `db` folder to a non-public location on your server. Just make sure to open up `data.php` and change the `DB_ROOT` function to reflect this new location.