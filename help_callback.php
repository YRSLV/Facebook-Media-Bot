<?php
class help_wrapper {
  public $file;
  function __construct($filename) {
    $this->file = $filename;
  }
  function get_help() {
    return file_get_contents($this->file);
  }
}

?>
