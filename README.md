Qlusterfuq
==========

This is the Qlusterfuq discussion group system. It's intended to be a simple, easy-to-deploy alternative to a Facebook group.

##How to set up Qlusterfuq

Eventually, we'll have a fancy install script that does all this for you. For now...

You'll need to go into the `db/set` folder and change the following:

-`admincontact` - Set this to an email address
-`daemon` - Set this to a no-reply email address
-`sitename` - Set this to the name of your site
-`systemroot` - Set this to the root URL of your Qlusterfuq site

You can also edit `regdesc` and `regintro` to change what appears on the "Apply to Join" page.

To create your first user, go to Apply to Join and go through the process. Your `admincontact` email will receive the join request. Approve it.

To make this new user an administrator, go to the `db/u/(User's ID)` folder and create a file called `admin`. Make the text of this file `yes`.

For security, set chmod the folder permissions of `db` to 700. You can also move the `db` folder to a non-public location on your server. Just make sure to open up `data.php` and change the `DB_ROOT` constant to reflect this new location.