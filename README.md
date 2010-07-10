web2project API
=======

**Since this README.md uses symlinks developers will need to be on *nix/linux platforms** -- **FRAPI performs well on Windows, however joining the two repos together might prove to be a bitch -- unless you can magically make decent symlinks on windows :D**

First of all, install FRAPI (Follow the instructions on : http://wiki.github.com/frapi/frapi/getting-started )

If you do not already have web2project installed (you can link to an existing install if you have one) then set it up following the instructions on : http://github.com/web2project/web2project/blob/master/INSTALLATION

Then in another directory (Let's call it WEB2PROJECT\_API\_PATH)  checkout the web2project-api repo (http://github.com/trevormorse/web2project-api) in another directory

Once this is done, go to the FRAPI\_PATH (Assuming you've followed the steps to installing FRAPI, you know what a FRAPI\_PATH is) and "cd" into:

	cd FRAPI_PATH/src/frapi/custom

Then remove all that's there

	rm -rf *

And now symlink all your WEB2PROJECT\_API\_PATH custom files into this directory like such

	ln -s WEB2PROJECT_API_PATH/src/* FRAPI_PATH/src/frapi/custom

The last thing to do is to tell the web2project api where your web2project installation is located. To do this open the WEB2PROJECT\_API\_PATH//custom/AllFiles.php file and find this line:

    define('W2P_INSTALL_DIR', '/var/www/web2project');

And update this to the directory of your web2project install.

Restart apache and go to your "admin.frapi" page. You will see the actions and modules that the web2project API has to offer. 

You can now start hacking and testing your actions (Which the code is located in the WEB2PROJECT\_API\_PATH//custom directory -- The actions (controllers) are in WEB2PROJECT\_API\_PATH/custom/Action)
