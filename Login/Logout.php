<?php
	require_once( '../pgConstants.php' );

  session_start();
  session_destroy();
  header( "location: " . URL_ROOT . "/admin/index.php" );
?>