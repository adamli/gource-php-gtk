<?php
/**
 * @author Igal Alkon <igal.alkon@gmail.com>
 * @version 0.06
 *
 * this is a php-gtk based program to easilly run gource repository virtulization
 * @todo need to add all other options to this
 *
 * why i used php-gtk you ask? well, first of all just to try this out, i'm a php web developer, and
 * php is my main language of use. i have never wrote any gtk applications. and found out that i can use
 * my php skills to do so. i'm not use how many of you can run php-gtk, since php-gtk v2.0 is still new
 * so you can check docs here: http://gtk.php.net
 * and you can also port this to C++ or something, but i will be really happy if php coders will join me and build this tool
 * so we all can get good and fast results from Gource (http://code.google.com/p/gource/)
*/

if (!class_exists('gtk')) {
    die("Please load the php-gtk2 module in your php.ini\r\n");
}

include_once 'settings.php';
include_once 'config.php';

function run_gource(GtkWindow $wnd, GtkEntry $txtRepositoryPath, $options)
{
    //fetch the values from the widgets into variables
    $strRepositoryPath = $txtRepositoryPath->get_text();

    //Do some error checking
    $errors = null;
    if (strlen($strRepositoryPath) == 0) {
        $errors .= "you need some path!.\r\n";
    }

    if ($errors !== null) {
		//save setting
		$settings = new Settings();
		$settings->set("last_repo_path", $strRepositoryPath);

        //There was at least one error.
        //We show a message box with the errors
        $dialog = new GtkMessageDialog($wnd, Gtk::DIALOG_MODAL,
            Gtk::MESSAGE_ERROR, Gtk::BUTTONS_OK, $errors);
        $dialog->set_markup(
            "The following errors occured:\r\n"
            . "<span foreground='red'>" . $errors . "</span>"
        );
        $dialog->run();
        $dialog->destroy();
    } else {

		$dialog = new GtkMessageDialog($wnd, Gtk::DIALOG_MODAL,
            Gtk::MESSAGE_ERROR, Gtk::BUTTONS_OK);
        $dialog->set_markup("running gource on repository...\npath: ".$txtRepositoryPath->get_text()."\n");
        $dialog->run();
        $dialog->destroy();

		$arr_options = array();
		foreach($options as $option_name => $option) {
			if($option->get_active() != 1) continue;
			
			switch($option_name) {
				case 'chkOptionHideFiles': $arr_options[] = "files"; break;
				case 'chkOptionHideFilenames': $arr_options[] = "filenames"; break;
				case 'chkOptionHideDirnames': $arr_options[] = "dirnames"; break;
				case 'chkOptionHideDates': $arr_options[] = "date"; break;
				case 'chkOptionHideProgress': $arr_options[] = "progress"; break;
				case 'chkOptionHideBloom': $arr_options[] = "bloom"; break;
				case 'chkOptionHideMouse': $arr_options[] = "mouse"; break;
				case 'chkOptionHideTree': $arr_options[] = "tree"; break;
				case 'chkOptionHideUsers': $arr_options[] = "users"; break;
				case 'chkOptionHideUsernames': $arr_options[] = "usernames"; break;
			}
		}
		$str_options = implode(',', $arr_options);

		// "&" is added to run gource in background
		if(!empty ($arr_options))
			system('gource '.$txtRepositoryPath->get_text()." --hide $str_options &", $gource_return);
		else
			system('gource '.$txtRepositoryPath->get_text()." &", $gource_return);

        //No error. You would need to hide the dialog now
        //instead of destroying it (because when you destroy it,
        //Gtk::main_quit() gets called) and show the main window
		//$wnd->destroy();
    }
}

function show_file_select(GtkWindow $wnd)
{
	$ConfigFile = new ConfigFile('select config file');
	$ConfigFile->show();
}

/**
 * main application section
 */

//Create the login window
$wnd = new GtkWindow();

$wnd->set_title('gource-php-gtk');
//Close the main loop when the window is destroyed
$wnd->connect_simple('destroy', array('gtk', 'main_quit'));


//Set up all the widgets we need
//The second parameter says that the underscore should be parsed as underline

//path
$lblRepositoryPath	= new GtkLabel('Repository Path', true);
$txtRepositoryPath	= new GtkEntry();

$settings = Settings::load();
$txtRepositoryPath->set_text($settings['last_repo_path']);

//options
$chkOptionHideFiles = new GtkCheckButton('Hide Files', true);
$chkOptionHideFilenames = new GtkCheckButton('Hide File Names', true);
$chkOptionHideDirnames = new GtkCheckButton('Hide Directories', true);
$chkOptionHideDates = new GtkCheckButton('Hide Dates', true);
$chkOptionHideProgress = new GtkCheckButton('Hide Progress', true);
$chkOptionHideBloom = new GtkCheckButton('Hide Bloom', true);
$chkOptionHideMouse = new GtkCheckButton('Hide Mouse', true);
$chkOptionHideTree = new GtkCheckButton('Hide Tree', true);
$chkOptionHideUsers = new GtkCheckButton('Hide Users', true);
$chkOptionHideUsernames = new GtkCheckButton('Hide Usernames', true);


//buttons
$btnRun			= new GtkButton('_Run');
$btnConfigFile	= new GtkButton('_Config File');
$btnQuit		= new GtkButton('_Quit');

//Which widget should be activated when the
// mnemonic (Alt+U or Alt+P) is pressed?
$lblRepositoryPath->set_mnemonic_widget($txtRepositoryPath);

//Destroy the window when the user clicks Cancel
$btnQuit->connect_simple('clicked', array($wnd, 'destroy'));

// what to do when RUN is pressed

$options = array(
	'chkOptionHideFiles' => $chkOptionHideFiles,
	'chkOptionHideFilenames' => $chkOptionHideFilenames,
	'chkOptionHideDirnames' => $chkOptionHideDirnames,
	'chkOptionHideDates' => $chkOptionHideDates,
	'chkOptionHideProgress' => $chkOptionHideProgress,
	'chkOptionHideBloom' => $chkOptionHideBloom,
	'chkOptionHideMouse' => $chkOptionHideMouse,
	'chkOptionHideTree' => $chkOptionHideTree,
	'chkOptionHideUsers' => $chkOptionHideUsers,
	'chkOptionHideUsernames' => $chkOptionHideUsernames,
);

$btnRun->connect_simple('clicked', 'run_gource', $wnd, $txtRepositoryPath, $options);
$btnConfigFile->connect_simple('clicked', 'show_file_select', $wnd);


//Lay out all the widgets in the table
$tbl = new GtkTable(2, 6);
$tbl->attach($lblRepositoryPath, 0, 1, 0, 1);
$tbl->attach($txtRepositoryPath, 1, 2, 0, 1);

//options attach to table
$tbl->attach($chkOptionHideFiles,		0, 1, 1, 2);
$tbl->attach($chkOptionHideFilenames,	0, 1, 2, 3);
$tbl->attach($chkOptionHideDirnames,	0, 1, 3, 4);
$tbl->attach($chkOptionHideDates,		0, 1, 4, 5);
$tbl->attach($chkOptionHideProgress,	0, 1, 5, 6);
$tbl->attach($chkOptionHideBloom,		1, 2, 1, 2);
$tbl->attach($chkOptionHideTree,		1, 2, 2, 3);
$tbl->attach($chkOptionHideUsers,		1, 2, 3, 4);
$tbl->attach($chkOptionHideUsernames,	1, 2, 4, 5);

//Add the buttons to a button box
$bbox = new GtkHButtonBox();
$bbox->set_layout(Gtk::BUTTONBOX_EDGE);
$bbox->add($btnQuit);
$bbox->add($btnConfigFile);
$bbox->add($btnRun);


//Add the table and the button box to a vbox
$vbox = new GtkVBox();
$vbox->pack_start($tbl);
$vbox->pack_start($bbox);


//Add the vbox to the window
$wnd->add($vbox);

//resize window
$wnd->move(450, 350);
$wnd->resize(600, 300);

//Show all widgets
$wnd->show_all();
//Start the main loop
Gtk::main();
?>