# joomla-xbAutosave
Autosave editor plugin for Joomla 3.9+ and 4.0+ version 3.0.0

Enables automatic saving of articles (com_content) whilst editing. Save interval configurable from 30sec (minimum) to as long as you like in seconds (keep it less than your session timeout so as not to loose work when you get distracted and forget to save)

Also enables Ctr+S and Cmd+S (mac) keystroke save function (instead of causing the browser to try and save the page as html)

Quirks:

1. This will probably not work with Joomla versions earlier than 3.9.x. The previous version is still available in the [Downloads](https://crosborne.uk/downloads/category/5-plugins) area on the CrOsborne site.
2. CmdS saving is flaky when using TinyMCE as the editor often grabs the keystroke first.
3. Data is saved to the database, not a temporary browser buffer until you close the article (as JCE does). This means that if you have versioning turned on you will also get a new version saved every N seconds which might mean that you loose the version from the previous session that you wanted to keep.
   It is recommended that if you are using Joomla's versioning when starting an editing session on an article you mark the previous version as "Keep" and unkeep any before that that you no longer need.



To download and install the current published version use 

https://crosborne.uk/xbautosave/dl and click the download button

To use "Install from URL" in Joomla try

https://www.crosborne.uk/downloads?download=3

The Joomla update system is used so you should get notified when a new version is released.



