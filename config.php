<?php
/**
 * @author Igal Alkon <igal.alkon@gmail.com>
 * @version 0.1
 *
 * a Class to save gource config files
 *
 * @todo make this make actual actions like saving and loading files
 *
*/

class ConfigFile {

	public $file = null;

	protected $fileSelect = null;

	public function __construct() {
		$this->fileSelect = new GtkFileSelection('select config file');
	}

	public function show()
	{
		//Adding a quit button that destroys the prompt
		$this->fileSelect->cancel_button->set_label('Cancel');
		$this->fileSelect->cancel_button->connect_simple('clicked', array($this->fileSelect, 'destroy'));

		//Add an OK button that displays the file selected on click
		$this->fileSelect->ok_button->set_label('Select Config');
		$this->fileSelect->ok_button->connect_simple('clicked', array($this, 'showFile'));

		//Show the prompt and start the main loop
		$this->fileSelect->show();
	}

	public function showFile()
	{
		$filePrompt = $this->fileSelect->ok_button->get_toplevel();
		$fileName = $filePrompt->get_filename();
		$message = new GtkMessageDialog(
			null,
			0,
			Gtk::MESSAGE_INFO,
			Gtk::BUTTONS_OK,
			'You selected: ' . $fileName
		);
		$message->run();
		$message->destroy();
	}
}

?>
