<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace theme_fordson\output;

use coding_exception;
use html_writer;
use tabobject;
use tabtree;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use single_select;
use paging_bar;
use url_select;
use context_course;
use pix_icon;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/course/renderer.php");
require_once($CFG->libdir. '/coursecatlib.php');

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_fordson
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {

        global $PAGE, $COURSE;

        $html = html_writer::start_tag('header', array('id' => 'page-header', 'class' => 'row'));
        $html .= html_writer::start_div('col-xs-12 p-a-1');
        $html .= html_writer::start_div('card');
        $html .= html_writer::start_div('headerfade');
        $html .= html_writer::start_div('card-block');
        if (!$PAGE->theme->settings->coursemanagementtoggle) {
            $html .= html_writer::div($this->context_header_settings_menu(), 'pull-xs-right context-header-settings-menu');
        } elseif (ISSET($COURSE->id) && $COURSE->id == 1) {
            $html .= html_writer::div($this->context_header_settings_menu(), 'pull-xs-right context-header-settings-menu');
        }
        $html .= html_writer::start_div('pull-xs-left');
        $html .= $this->context_header();
        $html .= html_writer::end_div();
        $pageheadingbutton = $this->page_heading_button();
        if (empty($PAGE->layout_options['nonavbar'])) {
            $html .= html_writer::start_div('clearfix w-100 pull-xs-left', array('id' => 'page-navbar'));
            $html .= html_writer::tag('div', $this->navbar(), array('class' => 'breadcrumb-nav'));
            //$html .= html_writer::tag('div', $this->thiscourse_menu(), array('class' => 'thiscourse'));
            $html .= html_writer::div($pageheadingbutton, 'breadcrumb-button pull-xs-right');
            $html .= html_writer::end_div();
        } else if ($pageheadingbutton) {
            $html .= html_writer::div($pageheadingbutton, 'breadcrumb-button nonavbar pull-xs-right');
        }
       $html .= html_writer::tag('div', $this->course_header(), array('id' => 'course-header'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();
        $html .= html_writer::end_div(); //headerfade
        $html .= html_writer::end_tag('header');
        return $html;
    }

    public function headerimage() {
        global $CFG, $COURSE;
                // Get course overview files.
        if (empty($CFG->courseoverviewfileslimit)) {
            return array();
        }
        require_once($CFG->libdir. '/filestorage/file_storage.php');
        require_once($CFG->dirroot. '/course/lib.php');
        $fs = get_file_storage();
        $context = context_course::instance($COURSE->id);
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'filename', false);
        if (count($files)) {
            $overviewfilesoptions = course_overviewfiles_options($COURSE->id);
            $acceptedtypes = $overviewfilesoptions['accepted_types'];
            if ($acceptedtypes !== '*') {
                // Filter only files with allowed extensions.
                require_once($CFG->libdir. '/filelib.php');
                foreach ($files as $key => $file) {
                    if (!file_extension_in_typegroup($file->get_filename(), $acceptedtypes)) {
                        unset($files[$key]);
                    }
                }
            }
            if (count($files) > $CFG->courseoverviewfileslimit) {
                // Return no more than $CFG->courseoverviewfileslimit files.
                $files = array_slice($files, 0, $CFG->courseoverviewfileslimit, true);
            }
        }

        // Get course overview files as images - set $courseimage.
        // The loop means that the LAST stored image will be the one displayed if >1 image file.
        $courseimage = '';
        foreach ($files as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $courseimage = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            }
        }

        // Create html for header.
        $html = html_writer::start_div('headerbkg');
        // If course image display it in separate div to allow css styling of inline style.
        if ($courseimage && theme_fordson_get_setting('showcourseheaderimage')) {
            $html .= html_writer::start_div('withimage', array(
                'style' => 'background-image: url("'.$courseimage.'"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'));
        }
        if ($courseimage && theme_fordson_get_setting('showcourseheaderimage')) {
            $html .= html_writer::end_div(); // End withimage inline style div.
        }
        $html .= html_writer::end_div();

        return $html;
        
    }


    /**
     * We don't like these...
     *
     */
     public function edit_button(moodle_url $url) {
        global $SITE, $PAGE, $USER, $CFG, $COURSE;
        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $btn = 'btn-danger';
            $title = get_string('editoff' , 'theme_fordson');
            $icon = 'fa-power-off';
        } else {
            $url->param('edit', 'on');
            $btn = 'btn-success';
            $title = get_string('editon' , 'theme_fordson');
            $icon = 'fa-edit';
        }
        return html_writer::tag('a', html_writer::start_tag('i', array('class' => $icon . ' fa fa-fw')) .
            html_writer::end_tag('i') . $title, array('href' => $url, 'class' => 'btn  ' . $btn, 'title' => $title));
        return $output;
    }


    /*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
     */
    protected function render_custom_menu(custom_menu $menu) {
        global $CFG;
        $context = $this->page->context;
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        $hasdisplaymycourses = (empty($this->page->theme->settings->displaymycourses)) ? false : $this->page->theme->settings->displaymycourses;
        if (isloggedin() && !isguestuser() && $hasdisplaymycourses) {
            $mycoursetitle = $this->page->theme->settings->mycoursetitle;
            if ($mycoursetitle == 'module') {
                $branchtitle = get_string('mymodules', 'theme_fordson');
            } else if ($mycoursetitle == 'unit') {
                $branchtitle = get_string('myunits', 'theme_fordson');
            } else if ($mycoursetitle == 'class') {
                $branchtitle = get_string('myclasses', 'theme_fordson');
            } else {
                $branchtitle = get_string('mycourses', 'theme_fordson');
            }
            $branchlabel = $branchtitle;
            $branchurl   = new moodle_url('/my/index.php');
            $branchsort  = 10000;
 
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            
            if ($courses = enrol_get_my_courses(NULL, 'fullname ASC')) {
                foreach ($courses as $course) {
                    if ($course->visible){
                        $branch->add(format_string($course->fullname), new moodle_url('/course/view.php?id='.$course->id), format_string($course->shortname));
                    }
                }
            } else {
                $noenrolments = get_string('noenrolments', 'theme_fordson');
                $branch->add('<em>'.$noenrolments.'</em>', new moodle_url('/'), $noenrolments);
            }
            if (is_siteadmin()) {
                        $branchtitle = get_string('siteadminquicklink', 'theme_fordson');
                        $branchlabel = $branchtitle;
                        $branchurl = new moodle_url('/admin/search.php');
                        $branch = $menu->add($branchlabel, $branchurl, $branchtitle);
        }

        }

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

        return $content;
    }
        

    protected function render_thiscourse_menu(custom_menu $menu) {
        global $CFG;

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

        return $content;
    }

    public function thiscourse_menu() {
        global $PAGE, $COURSE, $OUTPUT, $CFG;
        $menu = new custom_menu();
        $context = $this->page->context;

        if (isloggedin() && !isguestuser()) {
            if (!empty($PAGE->theme->settings->activitymenu)) {
                    if (ISSET($COURSE->id) && $COURSE->id > 1) {
                        $branchtitle = get_string('thiscourse', 'theme_fordson');
                        $branchlabel = $branchtitle;
                        $branchurl = new moodle_url('#');
                        $branch = $menu->add($branchlabel, $branchurl, $branchtitle, 10002);

                        $data = theme_fordson_get_course_activities();

                        foreach ($data as $modname => $modfullname) {
                            if ($modname === 'resources') {
                                
                                $branch->add($modfullname, new moodle_url('/course/resources.php', array('id' => $PAGE->course->id)));
                            } else {
    
                                $branch->add($modfullname, new moodle_url('/mod/'.$modname.'/index.php',
                                        array('id' => $PAGE->course->id)));
                            }
                        }
                }
            }
        }

        return $this->render_thiscourse_menu($menu);
    }

    public function social_icons() {
        global $PAGE;

        $hasfacebook    = (empty($PAGE->theme->settings->facebook)) ? false : $PAGE->theme->settings->facebook;
        $hastwitter     = (empty($PAGE->theme->settings->twitter)) ? false : $PAGE->theme->settings->twitter;
        $hasgoogleplus  = (empty($PAGE->theme->settings->googleplus)) ? false : $PAGE->theme->settings->googleplus;
        $haslinkedin    = (empty($PAGE->theme->settings->linkedin)) ? false : $PAGE->theme->settings->linkedin;
        $hasyoutube     = (empty($PAGE->theme->settings->youtube)) ? false : $PAGE->theme->settings->youtube;
        $hasflickr      = (empty($PAGE->theme->settings->flickr)) ? false : $PAGE->theme->settings->flickr;
        $hasvk          = (empty($PAGE->theme->settings->vk)) ? false : $PAGE->theme->settings->vk;
        $haspinterest   = (empty($PAGE->theme->settings->pinterest)) ? false : $PAGE->theme->settings->pinterest;
        $hasinstagram   = (empty($PAGE->theme->settings->instagram)) ? false : $PAGE->theme->settings->instagram;
        $hasskype       = (empty($PAGE->theme->settings->skype)) ? false : $PAGE->theme->settings->skype;
        $haswebsite     = (empty($PAGE->theme->settings->website)) ? false : $PAGE->theme->settings->website;
        $hasblog        = (empty($PAGE->theme->settings->blog)) ? false : $PAGE->theme->settings->blog;
        $hasvimeo       = (empty($PAGE->theme->settings->vimeo)) ? false : $PAGE->theme->settings->vimeo;
        $hastumblr      = (empty($PAGE->theme->settings->tumblr)) ? false : $PAGE->theme->settings->tumblr;
        $hassocial1     = (empty($PAGE->theme->settings->social1)) ? false : $PAGE->theme->settings->social1;
        $social1icon    = (empty($PAGE->theme->settings->socialicon1)) ? 'globe' : $PAGE->theme->settings->socialicon1;
        $hassocial2     = (empty($PAGE->theme->settings->social2)) ? false : $PAGE->theme->settings->social2;
        $social2icon    = (empty($PAGE->theme->settings->socialicon2)) ? 'globe' : $PAGE->theme->settings->socialicon2;
        $hassocial3     = (empty($PAGE->theme->settings->social3)) ? false : $PAGE->theme->settings->social3;
        $social3icon    = (empty($PAGE->theme->settings->socialicon3)) ? 'globe' : $PAGE->theme->settings->socialicon3;

        $socialcontext = [

            // If any of the above social networks are true, sets this to true.
            'hassocialnetworks' => ($hasfacebook || $hastwitter || $hasgoogleplus || $hasflickr || $hasinstagram
                || $hasvk || $haslinkedin || $haspinterest || $hasskype || $haslinkedin || $haswebsite || $hasyoutube
                || $hasblog ||$hasvimeo || $hastumblr || $hassocial1 || $hassocial2 || $hassocial3) ? true : false,

            'socialicons' => array(
                array('haslink' => $hasfacebook, 'linkicon' => 'facebook'),
                array('haslink' => $hastwitter, 'linkicon' => 'twitter'),
                array('haslink' => $hasgoogleplus, 'linkicon' => 'google-plus'),
                array('haslink' => $haslinkedin, 'linkicon' => 'linkedin'),
                array('haslink' => $hasyoutube, 'linkicon' => 'youtube'),
                array('haslink' => $hasflickr, 'linkicon' => 'flickr'),
                array('haslink' => $hasvk, 'linkicon' => 'vk'),
                array('haslink' => $haspinterest, 'linkicon' => 'pinterest'),
                array('haslink' => $hasinstagram, 'linkicon' => 'instagram'),
                array('haslink' => $hasskype, 'linkicon' => 'skype'),
                array('haslink' => $haswebsite, 'linkicon' => 'globe'),
                array('haslink' => $hasblog, 'linkicon' => 'bookmark'),
                array('haslink' => $hasvimeo, 'linkicon' => 'vimeo-square'),
                array('haslink' => $hastumblr, 'linkicon' => 'tumblr'),
                array('haslink' => $hassocial1, 'linkicon' => $social1icon),
                array('haslink' => $hassocial2, 'linkicon' => $social2icon),
                array('haslink' => $hassocial3, 'linkicon' => $social3icon),
            )
        ];

        return $this->render_from_template('theme_fordson/socialicons', $socialcontext);

    }

    public function fp_wonderbox() {
        global $PAGE;

        $context = $this->page->context;

        $hascreateicon    = (empty($PAGE->theme->settings->createicon && isloggedin() && has_capability('moodle/course:create', $context))) ? false : $PAGE->theme->settings->createicon;
        $createbuttonurl   = (empty($PAGE->theme->settings->createbuttonurl)) ? false : $PAGE->theme->settings->createbuttonurl;
        $createbuttontext   = (empty($PAGE->theme->settings->createbuttontext)) ? false : $PAGE->theme->settings->createbuttontext;

        $hasslideicon   = (empty($PAGE->theme->settings->slideicon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->slideicon;
        $slideiconbuttonurl   = 'data-toggle="collapse" data-target="#collapseExample';
        $slideiconbuttontext   = (empty($PAGE->theme->settings->slideiconbuttontext)) ? false : $PAGE->theme->settings->slideiconbuttontext;
        
        $hasnav1icon    = (empty($PAGE->theme->settings->nav1icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav1icon;
        $hasnav2icon     = (empty($PAGE->theme->settings->nav2icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav2icon;
        $hasnav3icon  = (empty($PAGE->theme->settings->nav3icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav3icon;
        $hasnav4icon    = (empty($PAGE->theme->settings->nav4icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav4icon;
        $hasnav5icon     = (empty($PAGE->theme->settings->nav5icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav5icon;
        $hasnav6icon      = (empty($PAGE->theme->settings->nav6icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav6icon;
        $hasnav7icon        = (empty($PAGE->theme->settings->nav7icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav7icon;
        $hasnav8icon   = (empty($PAGE->theme->settings->nav8icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav8icon;
        
        $nav1buttonurl   = (empty($PAGE->theme->settings->nav1buttonurl)) ? false : $PAGE->theme->settings->nav1buttonurl;
        $nav2buttonurl   = (empty($PAGE->theme->settings->nav2buttonurl)) ? false : $PAGE->theme->settings->nav2buttonurl;
        $nav3buttonurl   = (empty($PAGE->theme->settings->nav3buttonurl)) ? false : $PAGE->theme->settings->nav3buttonurl;
        $nav4buttonurl   = (empty($PAGE->theme->settings->nav4buttonurl)) ? false : $PAGE->theme->settings->nav4buttonurl;
        $nav5buttonurl   = (empty($PAGE->theme->settings->nav5buttonurl)) ? false : $PAGE->theme->settings->nav5buttonurl;
        $nav6buttonurl   = (empty($PAGE->theme->settings->nav6buttonurl)) ? false : $PAGE->theme->settings->nav6buttonurl;
        $nav7buttonurl   = (empty($PAGE->theme->settings->nav7buttonurl)) ? false : $PAGE->theme->settings->nav7buttonurl;
        $nav8buttonurl   = (empty($PAGE->theme->settings->nav8buttonurl)) ? false : $PAGE->theme->settings->nav8buttonurl;
        
        $nav1buttontext   = (empty($PAGE->theme->settings->nav1buttontext)) ? false : $PAGE->theme->settings->nav1buttontext;
        $nav2buttontext   = (empty($PAGE->theme->settings->nav2buttontext)) ? false : $PAGE->theme->settings->nav2buttontext;
        $nav3buttontext   = (empty($PAGE->theme->settings->nav3buttontext)) ? false : $PAGE->theme->settings->nav3buttontext;
        $nav4buttontext   = (empty($PAGE->theme->settings->nav4buttontext)) ? false : $PAGE->theme->settings->nav4buttontext;
        $nav5buttontext   = (empty($PAGE->theme->settings->nav5buttontext)) ? false : $PAGE->theme->settings->nav5buttontext;
        $nav6buttontext   = (empty($PAGE->theme->settings->nav6buttontext)) ? false : $PAGE->theme->settings->nav6buttontext;
        $nav7buttontext   = (empty($PAGE->theme->settings->nav7buttontext)) ? false : $PAGE->theme->settings->nav7buttontext;
        $nav8buttontext   = (empty($PAGE->theme->settings->nav8buttontext)) ? false : $PAGE->theme->settings->nav8buttontext;
        
        $searchurl = (new moodle_url('/course/search.php'))->out(true);
        $hasfpsearch = $PAGE->theme->settings->searchtoggle == 1;
        $fpsearch = get_string('fpsearch' , 'theme_fordson');
        $fptextbox  = (empty($PAGE->theme->settings->fptextbox && isloggedin())) ? false : format_text($PAGE->theme->settings->fptextbox);
        $fptextboxlogout  = (empty($PAGE->theme->settings->fptextboxlogout && !isloggedin())) ? false : format_text($PAGE->theme->settings->fptextboxlogout);
        $slidetextbox  = (empty($PAGE->theme->settings->slidetextbox && isloggedin())) ? false : format_text($PAGE->theme->settings->slidetextbox);
        $alertbox  = (empty($PAGE->theme->settings->alertbox)) ? false : format_text($PAGE->theme->settings->alertbox);

        $hasmarketing1  = (empty($PAGE->theme->settings->marketing1 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_text($PAGE->theme->settings->marketing1);
        $marketing1content  = (empty($PAGE->theme->settings->marketing1content)) ? false : format_text($PAGE->theme->settings->marketing1content);
        $marketing1buttontext  = (empty($PAGE->theme->settings->marketing1buttontext)) ? false : format_text($PAGE->theme->settings->marketing1buttontext);
        $marketing1buttonurl  = (empty($PAGE->theme->settings->marketing1buttonurl)) ? false : $PAGE->theme->settings->marketing1buttonurl;
        $marketing1target  = (empty($PAGE->theme->settings->marketing1target)) ? false : $PAGE->theme->settings->marketing1target;
        $marketing1icon  = (empty($PAGE->theme->settings->marketing1icon)) ? false : $PAGE->theme->settings->marketing1icon;
        $marketing1image = (empty($PAGE->theme->settings->marketing1image)) ? false : 'marketing1image';
        
        $hasmarketing2  = (empty($PAGE->theme->settings->marketing2 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_text($PAGE->theme->settings->marketing2);
        $marketing2content  = (empty($PAGE->theme->settings->marketing2content)) ? false : format_text($PAGE->theme->settings->marketing2content);
        $marketing2buttontext  = (empty($PAGE->theme->settings->marketing2buttontext)) ? false : format_text($PAGE->theme->settings->marketing2buttontext);
        $marketing2buttonurl  = (empty($PAGE->theme->settings->marketing2buttonurl)) ? false : $PAGE->theme->settings->marketing2buttonurl;
        $marketing2target  = (empty($PAGE->theme->settings->marketing2target)) ? false : $PAGE->theme->settings->marketing2target;
        $marketing2icon  = (empty($PAGE->theme->settings->marketing2icon)) ? false : $PAGE->theme->settings->marketing2icon;
        $marketing2image = (empty($PAGE->theme->settings->marketing2image)) ? false : 'marketing2image';

        $hasmarketing3  = (empty($PAGE->theme->settings->marketing3 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_text($PAGE->theme->settings->marketing3);
        $marketing3content  = (empty($PAGE->theme->settings->marketing3content)) ? false : format_text($PAGE->theme->settings->marketing3content);
        $marketing3buttontext  = (empty($PAGE->theme->settings->marketing3buttontext)) ? false : format_text($PAGE->theme->settings->marketing3buttontext);
        $marketing3buttonurl  = (empty($PAGE->theme->settings->marketing3buttonurl)) ? false : $PAGE->theme->settings->marketing3buttonurl;
        $marketing3target  = (empty($PAGE->theme->settings->marketing3target)) ? false : $PAGE->theme->settings->marketing3target;
        $marketing3icon  = (empty($PAGE->theme->settings->marketing3icon)) ? false : $PAGE->theme->settings->marketing3icon;
        $marketing3image = (empty($PAGE->theme->settings->marketing3image)) ? false : 'marketing3image';

        $hasmarketing4  = (empty($PAGE->theme->settings->marketing4 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_text($PAGE->theme->settings->marketing4);
        $marketing4content  = (empty($PAGE->theme->settings->marketing4content)) ? false : format_text($PAGE->theme->settings->marketing4content);
        $marketing4buttontext  = (empty($PAGE->theme->settings->marketing4buttontext)) ? false : format_text($PAGE->theme->settings->marketing4buttontext);
        $marketing4buttonurl  = (empty($PAGE->theme->settings->marketing4buttonurl)) ? false : $PAGE->theme->settings->marketing4buttonurl;
        $marketing4target  = (empty($PAGE->theme->settings->marketing4target)) ? false : $PAGE->theme->settings->marketing4target;
        $marketing4icon  = (empty($PAGE->theme->settings->marketing4icon)) ? false : $PAGE->theme->settings->marketing4icon;
        $marketing4image = (empty($PAGE->theme->settings->marketing4image)) ? false : 'marketing4image'; 

        $hasmarketing5  = (empty($PAGE->theme->settings->marketing5 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_text($PAGE->theme->settings->marketing5);
        $marketing5content  = (empty($PAGE->theme->settings->marketing5content)) ? false : format_text($PAGE->theme->settings->marketing5content);
        $marketing5buttontext  = (empty($PAGE->theme->settings->marketing5buttontext)) ? false : format_text($PAGE->theme->settings->marketing5buttontext);
        $marketing5buttonurl  = (empty($PAGE->theme->settings->marketing5buttonurl)) ? false : $PAGE->theme->settings->marketing5buttonurl;
        $marketing5target  = (empty($PAGE->theme->settings->marketing5target)) ? false : $PAGE->theme->settings->marketing5target;
        $marketing5icon  = (empty($PAGE->theme->settings->marketing5icon)) ? false : $PAGE->theme->settings->marketing5icon;
        $marketing5image = (empty($PAGE->theme->settings->marketing5image)) ? false : 'marketing5image';

        $hasmarketing6  = (empty($PAGE->theme->settings->marketing6 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_text($PAGE->theme->settings->marketing6);
        $marketing6content  = (empty($PAGE->theme->settings->marketing6content)) ? false : format_text($PAGE->theme->settings->marketing6content);
        $marketing6buttontext  = (empty($PAGE->theme->settings->marketing6buttontext)) ? false : format_text($PAGE->theme->settings->marketing6buttontext);
        $marketing6buttonurl  = (empty($PAGE->theme->settings->marketing6buttonurl)) ? false : $PAGE->theme->settings->marketing6buttonurl;
        $marketing6target  = (empty($PAGE->theme->settings->marketing6target)) ? false : $PAGE->theme->settings->marketing6target;
        $marketing6icon  = (empty($PAGE->theme->settings->marketing6icon)) ? false : $PAGE->theme->settings->marketing6icon;
        $marketing6image = (empty($PAGE->theme->settings->marketing6image)) ? false : 'marketing6image';

        
        $fp_wonderboxcontext = [

            'hasfptextbox' => (!empty($PAGE->theme->settings->fptextbox && isloggedin())),
            'fptextbox' => $fptextbox,

            'hasslidetextbox' => (!empty($PAGE->theme->settings->slidetextbox && isloggedin())),
            'slidetextbox' => $slidetextbox,

            'hasfptextboxlogout' => (!empty($PAGE->theme->settings->fptextboxlogout && !isloggedin())),
            'fptextboxlogout' => $fptextboxlogout,

            'hasalert' => (!empty($PAGE->theme->settings->alertbox && isloggedin())),
            'alertbox' => $alertbox,
            'searchurl' => $searchurl,
            'fpsearch' => $fpsearch,
            'hasfpsearch' => $hasfpsearch,

            'hasmarkettiles' => ($hasmarketing1 || $hasmarketing2 || $hasmarketing3 || $hasmarketing4 || $hasmarketing5 || $hasmarketing6) ? true : false,
            'markettiles' => array(
                array('hastile' => $hasmarketing1, 'tileicon' => $marketing1icon, 'tileimage' => $marketing1image, 'content' => $marketing1content, 'title' => $hasmarketing1, 'button' => "<a href = '$marketing1buttonurl' title = '$marketing1buttontext' alt='$marketing1buttontext' class='btn btn-primary' target='$marketing1target'> $marketing1buttontext </a>"),
                array('hastile' => $hasmarketing2, 'tileicon' => $marketing2icon, 'tileimage' => $marketing2image, 'content' => $marketing2content, 'title' => $hasmarketing2, 'button' => "<a href = '$marketing2buttonurl' title = '$marketing2buttontext' alt='$marketing2buttontext' class='btn btn-primary' target='$marketing2target'> $marketing2buttontext </a>"),
                array('hastile' => $hasmarketing3, 'tileicon' => $marketing3icon, 'tileimage' => $marketing3image, 'content' => $marketing3content, 'title' => $hasmarketing3, 'button' => "<a href = '$marketing3buttonurl' title = '$marketing3buttontext' alt='$marketing3buttontext' class='btn btn-primary' target='$marketing3target'> $marketing3buttontext </a>"),
                array('hastile' => $hasmarketing4, 'tileicon' => $marketing4icon, 'tileimage' => $marketing4image, 'content' => $marketing4content, 'title' => $hasmarketing4, 'button' => "<a href = '$marketing4buttonurl' title = '$marketing4buttontext' alt='$marketing4buttontext' class='btn btn-primary' target='$marketing4target'> $marketing4buttontext </a>"),
                array('hastile' => $hasmarketing5, 'tileicon' => $marketing5icon, 'tileimage' => $marketing5image, 'content' => $marketing5content, 'title' => $hasmarketing5, 'button' => "<a href = '$marketing5buttonurl' title = '$marketing5buttontext' alt='$marketing5buttontext' class='btn btn-primary' target='$marketing5target'> $marketing5buttontext </a>"),
                array('hastile' => $hasmarketing6, 'tileicon' => $marketing6icon, 'tileimage' => $marketing6image, 'content' => $marketing6content, 'title' => $hasmarketing6, 'button' => "<a href = '$marketing6buttonurl' title = '$marketing6buttontext' alt='$marketing6buttontext' class='btn btn-primary' target='$marketing6target'> $marketing6buttontext </a>"),
            ),

            // If any of the above social networks are true, sets this to true.
            'hasfpiconnav' => ($hasnav1icon || $hasnav2icon || $hasnav3icon || $hasnav4icon || $hasnav5icon
                || $hasnav6icon || $hasnav7icon || $hasnav8icon || $hascreateicon || $hasslideicon) ? true : false,
            'fpiconnav' => array(
                array('hasicon' => $hasnav1icon, 'linkicon' => $hasnav1icon, 'link' => $nav1buttonurl, 'linktext' => $nav1buttontext),
                array('hasicon' => $hasnav2icon, 'linkicon' => $hasnav2icon, 'link' => $nav2buttonurl, 'linktext' => $nav2buttontext),
                array('hasicon' => $hasnav3icon, 'linkicon' => $hasnav3icon, 'link' => $nav3buttonurl, 'linktext' => $nav3buttontext),
                array('hasicon' => $hasnav4icon, 'linkicon' => $hasnav4icon, 'link' => $nav4buttonurl, 'linktext' => $nav4buttontext),
                array('hasicon' => $hasnav5icon, 'linkicon' => $hasnav5icon, 'link' => $nav5buttonurl, 'linktext' => $nav5buttontext),
                array('hasicon' => $hasnav6icon, 'linkicon' => $hasnav6icon, 'link' => $nav6buttonurl, 'linktext' => $nav6buttontext),
                array('hasicon' => $hasnav7icon, 'linkicon' => $hasnav7icon, 'link' => $nav7buttonurl, 'linktext' => $nav7buttontext),
                array('hasicon' => $hasnav8icon, 'linkicon' => $hasnav8icon, 'link' => $nav8buttonurl, 'linktext' => $nav8buttontext),
            ),
            'fpcreateicon' => array(
                array('hasicon' => $hascreateicon, 'linkicon' => $hascreateicon, 'link' => $createbuttonurl, 'linktext' => $createbuttontext),
            ),
            'fpslideicon' => array(
                array('hasicon' => $hasslideicon, 'linkicon' => $hasslideicon, 'link' => $slideiconbuttonurl, 'linktext' => $slideiconbuttontext),
            ),

        ];
    
        
        return $this->render_from_template('theme_fordson/fpwonderbox', $fp_wonderboxcontext);

    }

    public function fp_marketingtiles() {
        global $PAGE;

        $hasmarketing1  = (empty($PAGE->theme->settings->marketing1 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_text($PAGE->theme->settings->marketing1);
        $marketing1content  = (empty($PAGE->theme->settings->marketing1content)) ? false : format_text($PAGE->theme->settings->marketing1content);
        $marketing1buttontext  = (empty($PAGE->theme->settings->marketing1buttontext)) ? false : format_text($PAGE->theme->settings->marketing1buttontext);
        $marketing1buttonurl  = (empty($PAGE->theme->settings->marketing1buttonurl)) ? false : $PAGE->theme->settings->marketing1buttonurl;
        $marketing1target  = (empty($PAGE->theme->settings->marketing1target)) ? false : $PAGE->theme->settings->marketing1target;
        $marketing1icon  = (empty($PAGE->theme->settings->marketing1icon)) ? false : $PAGE->theme->settings->marketing1icon;
        $marketing1image = (empty($PAGE->theme->settings->marketing1image)) ? false : 'marketing1image';
        
        $hasmarketing2  = (empty($PAGE->theme->settings->marketing2 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_text($PAGE->theme->settings->marketing2);
        $marketing2content  = (empty($PAGE->theme->settings->marketing2content)) ? false : format_text($PAGE->theme->settings->marketing2content);
        $marketing2buttontext  = (empty($PAGE->theme->settings->marketing2buttontext)) ? false : format_text($PAGE->theme->settings->marketing2buttontext);
        $marketing2buttonurl  = (empty($PAGE->theme->settings->marketing2buttonurl)) ? false : $PAGE->theme->settings->marketing2buttonurl;
        $marketing2target  = (empty($PAGE->theme->settings->marketing2target)) ? false : $PAGE->theme->settings->marketing2target;
        $marketing2icon  = (empty($PAGE->theme->settings->marketing2icon)) ? false : $PAGE->theme->settings->marketing2icon;
        $marketing2image = (empty($PAGE->theme->settings->marketing2image)) ? false : 'marketing2image';

        $hasmarketing3  = (empty($PAGE->theme->settings->marketing3 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_text($PAGE->theme->settings->marketing3);
        $marketing3content  = (empty($PAGE->theme->settings->marketing3content)) ? false : format_text($PAGE->theme->settings->marketing3content);
        $marketing3buttontext  = (empty($PAGE->theme->settings->marketing3buttontext)) ? false : format_text($PAGE->theme->settings->marketing3buttontext);
        $marketing3buttonurl  = (empty($PAGE->theme->settings->marketing3buttonurl)) ? false : $PAGE->theme->settings->marketing3buttonurl;
        $marketing3target  = (empty($PAGE->theme->settings->marketing3target)) ? false : $PAGE->theme->settings->marketing3target;
        $marketing3icon  = (empty($PAGE->theme->settings->marketing3icon)) ? false : $PAGE->theme->settings->marketing3icon;
        $marketing3image = (empty($PAGE->theme->settings->marketing3image)) ? false : 'marketing3image';

        $hasmarketing4  = (empty($PAGE->theme->settings->marketing4 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_text($PAGE->theme->settings->marketing4);
        $marketing4content  = (empty($PAGE->theme->settings->marketing4content)) ? false : format_text($PAGE->theme->settings->marketing4content);
        $marketing4buttontext  = (empty($PAGE->theme->settings->marketing4buttontext)) ? false : format_text($PAGE->theme->settings->marketing4buttontext);
        $marketing4buttonurl  = (empty($PAGE->theme->settings->marketing4buttonurl)) ? false : $PAGE->theme->settings->marketing4buttonurl;
        $marketing4target  = (empty($PAGE->theme->settings->marketing4target)) ? false : $PAGE->theme->settings->marketing4target;
        $marketing4icon  = (empty($PAGE->theme->settings->marketing4icon)) ? false : $PAGE->theme->settings->marketing4icon;
        $marketing4image = (empty($PAGE->theme->settings->marketing4image)) ? false : 'marketing4image'; 

        $hasmarketing5  = (empty($PAGE->theme->settings->marketing5 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_text($PAGE->theme->settings->marketing5);
        $marketing5content  = (empty($PAGE->theme->settings->marketing5content)) ? false : format_text($PAGE->theme->settings->marketing5content);
        $marketing5buttontext  = (empty($PAGE->theme->settings->marketing5buttontext)) ? false : format_text($PAGE->theme->settings->marketing5buttontext);
        $marketing5buttonurl  = (empty($PAGE->theme->settings->marketing5buttonurl)) ? false : $PAGE->theme->settings->marketing5buttonurl;
        $marketing5target  = (empty($PAGE->theme->settings->marketing5target)) ? false : $PAGE->theme->settings->marketing5target;
        $marketing5icon  = (empty($PAGE->theme->settings->marketing5icon)) ? false : $PAGE->theme->settings->marketing5icon;
        $marketing5image = (empty($PAGE->theme->settings->marketing5image)) ? false : 'marketing5image';

        $hasmarketing6  = (empty($PAGE->theme->settings->marketing6 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_text($PAGE->theme->settings->marketing6);
        $marketing6content  = (empty($PAGE->theme->settings->marketing6content)) ? false : format_text($PAGE->theme->settings->marketing6content);
        $marketing6buttontext  = (empty($PAGE->theme->settings->marketing6buttontext)) ? false : format_text($PAGE->theme->settings->marketing6buttontext);
        $marketing6buttonurl  = (empty($PAGE->theme->settings->marketing6buttonurl)) ? false : $PAGE->theme->settings->marketing6buttonurl;
        $marketing6target  = (empty($PAGE->theme->settings->marketing6target)) ? false : $PAGE->theme->settings->marketing6target;
        $marketing6icon  = (empty($PAGE->theme->settings->marketing6icon)) ? false : $PAGE->theme->settings->marketing6icon;
        $marketing6image = (empty($PAGE->theme->settings->marketing6image)) ? false : 'marketing6image';

        $fp_marketingtiles = [

            'hasmarkettiles' => ($hasmarketing1 || $hasmarketing2 || $hasmarketing3 || $hasmarketing4 || $hasmarketing5 || $hasmarketing6) ? true : false,

            'markettiles' => array(
                array('hastile' => $hasmarketing1, 'tileicon' => $marketing1icon, 'tileimage' => $marketing1image, 'content' => $marketing1content, 'title' => $hasmarketing1, 'button' => "<a href = '$marketing1buttonurl' title = '$marketing1buttontext' alt='$marketing1buttontext' class='btn btn-primary' target='$marketing1target'> $marketing1buttontext </a>"),
                array('hastile' => $hasmarketing2, 'tileicon' => $marketing2icon, 'tileimage' => $marketing2image, 'content' => $marketing2content, 'title' => $hasmarketing2, 'button' => "<a href = '$marketing2buttonurl' title = '$marketing2buttontext' alt='$marketing2buttontext' class='btn btn-primary' target='$marketing2target'> $marketing2buttontext </a>"),
                array('hastile' => $hasmarketing3, 'tileicon' => $marketing3icon, 'tileimage' => $marketing3image, 'content' => $marketing3content, 'title' => $hasmarketing3, 'button' => "<a href = '$marketing3buttonurl' title = '$marketing3buttontext' alt='$marketing3buttontext' class='btn btn-primary' target='$marketing3target'> $marketing3buttontext </a>"),
                array('hastile' => $hasmarketing4, 'tileicon' => $marketing4icon, 'tileimage' => $marketing4image, 'content' => $marketing4content, 'title' => $hasmarketing4, 'button' => "<a href = '$marketing4buttonurl' title = '$marketing4buttontext' alt='$marketing4buttontext' class='btn btn-primary' target='$marketing4target'> $marketing4buttontext </a>"),
                array('hastile' => $hasmarketing5, 'tileicon' => $marketing5icon, 'tileimage' => $marketing5image, 'content' => $marketing5content, 'title' => $hasmarketing5, 'button' => "<a href = '$marketing5buttonurl' title = '$marketing5buttontext' alt='$marketing5buttontext' class='btn btn-primary' target='$marketing5target'> $marketing5buttontext </a>"),
                array('hastile' => $hasmarketing6, 'tileicon' => $marketing6icon, 'tileimage' => $marketing6image, 'content' => $marketing6content, 'title' => $hasmarketing6, 'button' => "<a href = '$marketing6buttonurl' title = '$marketing6buttontext' alt='$marketing6buttontext' class='btn btn-primary' target='$marketing6target'> $marketing6buttontext </a>"),
            ),
        ];

        return $this->render_from_template('theme_fordson/fpmarkettiles', $fp_marketingtiles);
    }

    public function fp_slideshow() {
        global $PAGE;

        $slideshowon = $PAGE->theme->settings->showslideshow == 1;

        $hasslide1 = (empty($PAGE->theme->settings->slide1title)) ? false : format_text($PAGE->theme->settings->slide1title);
        $slide1content = (empty($PAGE->theme->settings->slide1content)) ? false : format_text($PAGE->theme->settings->slide1content);

        $hasslide2 = (empty($PAGE->theme->settings->slide2title)) ? false : format_text($PAGE->theme->settings->slide2title);
        $slide2content = (empty($PAGE->theme->settings->slide2content)) ? false : format_text($PAGE->theme->settings->slide2content);

        $hasslide3 = (empty($PAGE->theme->settings->slide3title)) ? false : format_text($PAGE->theme->settings->slide3title);
        $slide3content = (empty($PAGE->theme->settings->slide3content)) ? false : format_text($PAGE->theme->settings->slide3content);

        $fp_slideshow = [

            'hasfpslideshow' => $slideshowon,

            'hasslide1' => $hasslide1 ? true : false,
            'hasslide2' => $hasslide2 ? true : false,
            'hasslide3' => $hasslide3 ? true : false,

            'slide1'    => array('slidetitle' => $hasslide1, 'slidecontent' => $slide1content),
            'slide2'    => array('slidetitle' => $hasslide2, 'slidecontent' => $slide2content),
            'slide3'    => array('slidetitle' => $hasslide3, 'slidecontent' => $slide3content),

        ];

        return $this->render_from_template('theme_fordson/slideshow', $fp_slideshow);
    }

    public function teacherdash() {
        global $PAGE, $COURSE, $CFG, $DB;

        $context = $this->page->context;
        $haseditcog = $PAGE->theme->settings->courseeditingcog;
        $editcog = html_writer::div($this->context_header_settings_menu(), 'pull-xs-right context-header-settings-menu');
        $thiscourse = html_writer::tag('div', $this->thiscourse_menu(), array('class' => 'thiscourse'));
        $showincourseonly = isset($COURSE->id) && $COURSE->id > 1;
        $globalhaseasyenrollment = enrol_get_plugin('easy');
        $coursehaseasyenrollment = '';
        if($globalhaseasyenrollment) {
            $coursehaseasyenrollment = $DB->record_exists('enrol', array('courseid' => $COURSE->id, 'enrol' => 'easy'));
            $easyenrollinstance = $DB->get_record('enrol', array('courseid' => $COURSE->id, 'enrol' => 'easy'));
        }
        //link catagories
        $haspermission = has_capability('enrol/category:config', $context) && $PAGE->theme->settings->coursemanagementtoggle && isset($COURSE->id) && $COURSE->id > 1;
        $togglebutton = get_string('coursemanagementbutton', 'theme_fordson');
        $userlinks = get_string('userlinks', 'theme_fordson');
        $userlinksdesc = get_string('userlinks_desc', 'theme_fordson');
        $qbank = get_string('qbank', 'theme_fordson');
        $qbankdesc = get_string('qbank_desc', 'theme_fordson');
        $badges = get_string('badges', 'theme_fordson');
        $badgesdesc = get_string('badges_desc', 'theme_fordson');
        $coursemanage = get_string('coursemanage', 'theme_fordson');
        $coursemanagedesc = get_string('coursemanage_desc', 'theme_fordson');
        $coursemanagementmessage = (empty($PAGE->theme->settings->coursemanagementtextbox)) ? false : format_text($PAGE->theme->settings->coursemanagementtextbox);

        //user links
        
        if($coursehaseasyenrollment && isset($COURSE->id) && $COURSE->id > 1){
            $easycodetitle = get_string('header_coursecodes', 'enrol_easy');
            $easycodelink = new moodle_url('/enrol/editinstance.php', array('courseid' => $PAGE->course->id, 'id' => $easyenrollinstance->id, 'type' =>'easy'));
        }
        $gradestitle = get_string('gradesoverview', 'gradereport_overview');
        $gradeslink = new moodle_url('/grade/report/grader/index.php', array('id' => $PAGE->course->id));
        $enroltitle = get_string('enrolledusers', 'enrol');
        $enrollink = new moodle_url('/enrol/users.php', array('id' => $PAGE->course->id));
        $grouptitle = get_string('groups', 'group');
        $grouplink = new moodle_url('/group/index.php', array('id' => $PAGE->course->id));
        $enrolmethodtitle = get_string('enrolmentinstances', 'enrol');
        $enrolmethodlink = new moodle_url('/enrol/instances.php', array('id' => $PAGE->course->id));
        
        //user reports
        $logstitle = get_string('logs', 'moodle');
        $logslink = new moodle_url('/report/log/index.php', array('id' => $PAGE->course->id));
        $livelogstitle = get_string('loglive:view', 'report_loglive');
        $livelogslink = new moodle_url('/report/loglive/index.php', array('id' => $PAGE->course->id));
        $participationtitle = get_string('participation:view', 'report_participation');
        $participationlink = new moodle_url('/report/participation/index.php', array('id' => $PAGE->course->id));
        $activitytitle = get_string('outline:view', 'report_outline');
        $activitylink = new moodle_url('/report/outline/index.php', array('id' => $PAGE->course->id));

        //questionbank
        $qbanktitle = get_string('questionbank', 'question');
        $qbanklink = new moodle_url('/question/edit.php', array('courseid' => $PAGE->course->id));
        $qcattitle = get_string('questioncategory', 'question');
        $qcatlink = new moodle_url('/question/category.php', array('courseid' => $PAGE->course->id));
        $qimporttitle = get_string('import', 'question');
        $qimportlink = new moodle_url('/question/import.php', array('courseid' => $PAGE->course->id));
        $qexporttitle = get_string('export', 'question');
        $qexportlink = new moodle_url('/question/export.php', array('courseid' => $PAGE->course->id));
        //manage course
        $courseadmintitle = get_string('courseadministration', 'moodle');
        $courseadminlink = new moodle_url('/course/admin.php', array('courseid' => $PAGE->course->id));
        $courseresettitle = get_string('reset', 'moodle');
        $courseresetlink = new moodle_url('/course/reset.php', array('id' => $PAGE->course->id));
        $coursebackuptitle = get_string('backup', 'moodle');
        $coursebackuplink = new moodle_url('/backup/backup.php', array('id' => $PAGE->course->id));
        $courserestoretitle = get_string('restore', 'moodle');
        $courserestorelink = new moodle_url('/backup/restorefile.php', array('contextid' => $PAGE->context->id));
        $courseimporttitle = get_string('import', 'moodle');
        $courseimportlink = new moodle_url('/backup/import.php', array('id' => $PAGE->course->id));
        $courseedittitle = get_string('editcoursesettings', 'moodle');
        $courseeditlink = new moodle_url('/course/edit.php', array('id' => $PAGE->course->id));
        //badges
        $badgemanagetitle = get_string('managebadges', 'badges');
        $badgemanagelink = new moodle_url('/badges/index.php?type=2', array('id' => $PAGE->course->id));
        $badgeaddtitle = get_string('newbadge', 'badges');
        $badgeaddlink = new moodle_url('/badges/newbadge.php?type=2', array('id' => $PAGE->course->id));
        //misc
        $recyclebintitle = get_string('pluginname', 'tool_recyclebin');
        $recyclebinlink = new moodle_url('/admin/tool/recyclebin/index.php', array('contextid' => $PAGE->context->id));
        $filtertitle = get_string('filtersettings', 'filters');
        $filterlink = new moodle_url('/filter/manage.php', array('contextid' => $PAGE->context->id));

        $dashlinks = [
        'showincourseonly' =>$showincourseonly,
        'haspermission' => $haspermission,
        'thiscourse' => $thiscourse,
        'haseditcog' => $haseditcog,
        'editcog' => $editcog,
        'togglebutton' => $togglebutton,
        'userlinkstitle' => $userlinks,
        'userlinksdesc' => $userlinksdesc,
        'qbanktitle' => $qbank,
        'qbankdesc' => $qbankdesc,
        'badgestitle' => $badges,
        'badgesdesc' => $badgesdesc,
        'coursemanagetitle' => $coursemanage,
        'coursemanagedesc' => $coursemanagedesc,
        'coursemanagementmessage' =>$coursemanagementmessage,

        'dashlinks' => array(
                array('hasuserlinks' => $gradestitle, 'title' => $gradestitle, 'url' => $gradeslink),
                array('hasuserlinks' => $enroltitle, 'title' => $enroltitle, 'url' => $enrollink),
                array('hasuserlinks' => $grouptitle, 'title' => $grouptitle, 'url' => $grouplink),
                array('hasuserlinks' => $enrolmethodtitle, 'title' => $enrolmethodtitle, 'url' => $enrolmethodlink),
                array('hasuserlinks' => $logstitle, 'title' => $logstitle, 'url' => $logslink),
                array('hasuserlinks' => $livelogstitle, 'title' => $livelogstitle, 'url' => $livelogslink),
                array('hasuserlinks' => $participationtitle, 'title' => $participationtitle, 'url' => $participationlink),
                array('hasuserlinks' => $activitytitle, 'title' => $activitytitle, 'url' => $activitylink),
                array('hasqbanklinks' => $qbanktitle, 'title' => $qbanktitle, 'url' => $qbanklink),
                array('hasqbanklinks' => $qcattitle, 'title' => $qcattitle, 'url' => $qcatlink),
                array('hasqbanklinks' => $qimporttitle, 'title' => $qimporttitle, 'url' => $qimportlink),
                array('hasqbanklinks' => $qexporttitle, 'title' => $qexporttitle, 'url' => $qexportlink),
                array('hascoursemanagelinks' => $courseedittitle, 'title' => $courseedittitle, 'url' => $courseeditlink),
                array('hascoursemanagelinks' => $courseadmintitle, 'title' => $courseadmintitle, 'url' => $courseadminlink),
                array('hascoursemanagelinks' => $courseresettitle, 'title' => $courseresettitle, 'url' => $courseresetlink),
                array('hascoursemanagelinks' => $coursebackuptitle, 'title' => $coursebackuptitle, 'url' => $coursebackuplink),
                array('hascoursemanagelinks' => $courserestoretitle, 'title' => $courserestoretitle, 'url' => $courserestorelink),
                array('hascoursemanagelinks' => $courseimporttitle, 'title' => $courseimporttitle, 'url' => $courseimportlink),
                array('hascoursemanagelinks' => $recyclebintitle, 'title' => $recyclebintitle, 'url' => $recyclebinlink),
                array('hascoursemanagelinks' => $filtertitle, 'title' => $filtertitle, 'url' => $filterlink),
                array('hasbadgelinks' => $badgemanagetitle, 'title' => $badgemanagetitle, 'url' => $badgemanagelink),
                array('hasbadgelinks' => $badgeaddtitle, 'title' => $badgeaddtitle, 'url' => $badgeaddlink),
            ),
        ];
        
        if ($globalhaseasyenrollment && $coursehaseasyenrollment) {
            $dashlinks['dashlinks'][] = array('haseasyenrollment' => $coursehaseasyenrollment, 'title' => $easycodetitle, 'url' => $easycodelink);

        }
        
            return $this->render_from_template('theme_fordson/teacherdash', $dashlinks );
        
    }

    public function footnote() {
        global $PAGE;
        $footnote = '';
        $footnote    = (empty($PAGE->theme->settings->footnote)) ? false : format_text($PAGE->theme->settings->footnote);
        return $footnote;
    }
   
}
