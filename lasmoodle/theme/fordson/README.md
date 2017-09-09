THEME_Fordson
===========

# Fordson

Fordson is a child theme for Moodle's core theme Boost.

It is built on Boostrap4 and Mustache templates.

Here are the main enhancements:
* Presets - Both uploaded and a set of pre-installed presets to quickly adjust colors
* Colours - A variety of color pickers to help customize and fine tune presets
* Images - Custom Login, Custom Page Header, Custom Course Header, and Custom Page Background image uploads
* Social Icons - Quickly add all your social media buttons in the footer
* Enhanced Frontpage - Navigation Iconbar, Custom Textbox, Alertbox, Six Marketing Tiles
* Customized Course Header Image - Teachers can upload an image into their course summary files and it will automatically be used in the header of their course.

## Recommended Settings for Best Results
These settings below are found in the Moodle Site Administration Pages.  These are not related to the theme but will help bring out the best of Fordson.  

* defaulthomepage = SITE (Default homepage for users.  If set to Dashboard your users will not see the Enhanced Homepage upon login.)
* frontpage = none (Frontpage items to show. Part of Moodle Frontpage Settings tab.)
* frontpageloggedin = none or Enrolled Courses (Frontpage items to show when logged in. Part of Moodle Frontpage Settings tab.)
* Optional: forcelogin = Checked (This forces users to login before seeing the homepage.)

Fordson is a child theme of Boost.  
This means when Moodle updates the core Boost theme those changes will be applied to Fordson as well in most cases.

# Install from Github
Click on the button to "Clone or Download" https://github.com/dbnschools/moodle-theme_fordson . When downloaded to your computer, unzip it. It should create a folder named "moodle-theme_fordson-master". Rename the folder so that it is "fordson" (without quotes). You can FTP that folder to your moodle site in /moodle/theme/ directory. Or you can create a new ZIP file of the "fordson" folder and upload and install it via the Plugin Administration in Site Administration. 

## Fordson 1.4.6
* New releases for Fordson on Moodle 3.2 will only be bug fixes.  All new development and features require Moodle 3.3 and components only in Moodle 3.3 such as the new course overview block to help build the student dashboard slider and other features. 
* Fixed issue with Easy Enrollment integration where Easy Enrollment is installed but not used on a particular course.  This led to an unknown variable issue.

## Fordson 1.4.5
* Fixed compatibility with collapsed topic course format thanks to Gareth
* Fixed issue when Easy Enrollment plugin was not installed and the course management panel integration

## Fordson 1.4.4
* New integrations with Easy Enrollment plugin: When easy enrollment is activated on a course a new link appears which will show the teacher all their enrollment codes for the course.  This is in the header area just above the Turn Editing on button in the upper right of the course page.
* Prepped and ready for the release of Easy Enrollment Plugin on github
* Cleaned up classes and functions as to not override core more than needed

## Fordson 1.4.3
* Added custom textbox to provide teachers a message in the Course Management Panel. 
* Added new items to Course Management Panel.
* Added logic to show/hide contextual menu when using the Course Management Panel.  This will hide the Editing Cog when using Course Management Panel and display it when not activated.  It also shows the menu only on the homepage when using Course Management Panel. You can also force the contextual menu to appear with the course management panel menu.
* Moved This Course drop down to be with the Course Management Panel menu. 
* fixed styling issues with presets and the new course management panel menu. 
* fixed responsive issues and the new course management panel menu. 
* Turn Editing On button is now visible at all times.  Previously it would hide when viewing on a phone but since you can now hide the contextual course editing cog it is important to be able to turn editing on from the button when not accessible from the menu. 
* Fixed ADA issue with each icon on homepage having the same ID=button.  Thanks Emma Richardson.
* Fixed HeaderLogo image on small screen sizes.  Thanks Emma Richardson.
 

## Fordson 1.4.2
* Introducing a brand new navigation concept: Teacher Course Management Panel.  This intuitive sliding panel displays a custom list of links that help the teacher manage the course.  It appears in the upper right side of the page just above the Turn Editing On button.  This is the first implementation and future plans include a custom textbox, icons, better descriptions, and other enahncements to make managing your course easier for the teachers.  
* Added Bootstrap Carousel left and right controls to autoplay slideshow.
* Multiple styling enhancements.


## Fordson 1.4.1
* Added Block Column width to control block widths in Fordson Content Areas page
* Added Boostrap Carousel slideshow with three slides to frontpage. Add a title, description, and image background.  Image background will scale with the size of the page and is not meant to contain actual content.  To be in ADA compliance we prefer to use the description text to display information and not text in a image.  
* Custom Logo upload for Fordson only appears on homepage above new slideshow so that once in a course the student is focused on content
* Moved Learning Content Spacing setting from Image Setting page to Content Settings Page since it is the spacing between the top of the page and the actual page content.
* New default header image

## Fordson 1.4.0
* Major changes to layout!  This is a major change in direction for the Fordson theme.
* Added ability to turn off teacher course files in header so that all courses get the default image in the header area.
* Version 1.4.0 initially ships with two very distinct style presets:  Default and e-Learner.  E-Learner is uniquely styled to utlize a full screen background image set by the site admin and/or teacher at the course level.
* Added a Logo uploader that will appear in the header area.  See the Custom Image Settings tab in site admin. 400px by 125px is the size of the logo image area.

## Fordson 1.3.1
* Added MyCourse drop down to the top navigation bar.  Settings are on the Menu page within the theme.
* Fixed duplicate function issue.
* Adjusted some default settings on install.

## Fordson 1.3.0
* Customized Nav Drawer - Add and remove items from the Boost navigation drawer.  Ability to have customizations appear on all pages, frontpage only, course pages only. Special thanks to Alexander Bias with https://github.com/moodleuulm/moodle-local_boostnavigation and Carlos Escobedo with https://moodle.org/plugins/local_navigation.  I was able to combine these two plugins to remove default menu items and add new menu items.  Because this is done with a theme we also added a toggle to allow you to determine where the customizations appear.

## Fordson 1.2.9b
* Revisited and removed entire tag for This Course drop down title.
* Fixed noimg URL for displaying tiled courses where no background image is used.
* fixed course renderer when cat ID=0 undefined issue.

## Fordson 1.2.9a
* Fixed closing div tag introduced in version 1.2.9 in This Course drop down.

## Fordson 1.2.9
* Fixed Moodle 3.2.1+ changes to Boost theme that impact Fordson.  Issue related to header elements breaking such as the This Course Dropdown.
* Continued improvements to Default Style Preset

## Fordson 1.2.8
* Made changes so that Marketing tiles, frontpage textboxes, and other elements are more compatible with language plugins.
* Fixed issue with "This Course" drop-down menu appearing on pages other than the homepage of the course.  This caused problems when viewing courses on smaller screens and running out of space in the header.  

## Fordson 1.2.7
* Fixed default URL's used in frontpage icons to use site root url.
* Fixed course display when using the search function

## Fordson 1.2.6
* Added Frontpage Available courses box styling.  Setting in theme admin settings will allow Site admin to switch between default Moodle presentation of available courses and the new Fordson display which is a tiled display in a grid.  SPECIAL THANKS TO José Miguel Dager Montoya for helping get this feature started.
* Added Category Icon view. 
* Added icon chooser in theme admin for course categories.
* Added Frontpage Course tile height which allows you to make them smaller if needed.
* Added Course Title and Summary Trim value for the display of courses in category view.
* Added toggle to show or hide course tile tooltips.


## Fordson v1.2.5
* Added language strings for default text of icon navigation
* Changed font awesome icons from text field to drop down select to make things very simple.  Also included the option to remove the icons next to section and header titles from the drop down. 
* Upgraded to font awesome 4.7
* Added Frontpage Available courses box styling.  Setting in theme admin settings will allow Site admin to switch between default Moodle presentation of available courses and the new Fordson display.  SPECIAL THANKS TO José Miguel Dager Montoya for helping get this feature started.


## Fordson v1.2.4
* Fixed issue with header information getting distorted on smaller screen sizes with small header image height set.  The text would sometimes get cut off.  
* Fixed Footer color selector to use a common SCSS $footer-bg for all presets.

## Fordson v1.2.3
* Added Font-Awesome icons for each section in a course as well as the main header title.  Each can be set using Fontawesome unicode with parenthesis around the unicode. Examples in the setting description are provided.
* Continued enhancements of the preset style sheets.  Specifically for default and evolve-D.
* Social icon links now open in new window.
* Fixed logo navbar display where image and text were not aligning properly in the center of the navbar.

## Fordson v1.2.2  non published
* Style Presets have been refined for better control of colors and variables.  
* NEW Style Preset: Evolve-D.  This preset has many of the style elements of the Evolve-D theme for those users who might want to migrate from Evolve-D to Fordson.  Switching and upgrading will utilize the new features of Boost but have a similar look and feel of Evolve-D.

## Fordson v1.2.1
* Removed default colors being set on install.  This created issues when swapping out presets as the colors would override the preset.
* Created new presets: The Rouge, The Rouge X, Ford Field, Ford Field X, City Hall, City Hall X, Michigan Ave, Michigan Ave X
* Fixed undesired headers being made vertical when using X series presets

## Fordson v1.2.0
* Removed Bootswatch Presets due to accessibility issues.  Will be hand crafting presets with "purpose" such as elementary, middle, and high schools, college, business, etc.
* Fixed accessibility contrast issue for login text in top navigation bar

## Fordson v1.1.9
* Fixed issue where language menu appeared in two spots that used the same function to render a menu.  This was fixed and now functions as expected.

## Fordson v1.1.8
* Icon navigation bad will no longer show for users who login as guest.
* Removed color chooser that was not used.
* Review and corrected some language strings.

## Fordson v1.1.7
* Made This Course Menu customizable with the abilty to checkoff menu items in Fordson Theme Admin page.  

## Fordson v1.1.6
* Fixed activity edit menu had disappeared during a Boost update.  Fixed core renderer on Fordson.

## Fordson v1.1.5
* Added This Course(Course Activities) drop down next to breadcrumbs in header
* Added Theme Admin Setting to toggle on/off Course Activity Menu
* Fixed Bootstrap Presets from causing issues

## Fordson v1.1.4
* Fixed duplicate language string

## Fordson v1.1.3
* Added homepage slider feature which allows a special button in the Icon Navigation Bar to show or hide a text box which slides down from the Icon Navigation bar.  Useful for featured courses, help information, and other things that need attention but do not need to be visible all the time.
* Added additional height settings to fine tune header image height

## Fordson v1.1.2
* Added Icon Navigation Width Setting - $fpicon-width and $fpiconcreate-width
* Added Heading Font Color Setting
* Added Page Heading H1 Color Setting
* Added several new color pickers
* Added 16 new presets to choose from
* Added Homepage Course Search Box Toggle Checkbox to show or hide searchbar

## Fordson v1.1.1
* Removed un-needed layout and template files.
* Tidy up code
* Cleaned up & organized lang file for better translation
* Moved SCSS to styles.scss so that Fordson will work with default presets and user uploaded presets.  This includes marketing tiles, navigation icons, and other custom elements added to Fordson.

## Fordson v1.1
* Moved "Create A Course" button to right of the Icon Navigation Bar
