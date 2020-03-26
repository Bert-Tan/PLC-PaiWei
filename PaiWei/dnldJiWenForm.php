<?php
/**********************************************************
 *       Downloading JiWen/SuWen Main Page - Chinese      *
 **********************************************************/
 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( "PaiWei_DBfuncs.php" );
	
	session_start(); // create or retrieve

	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( "location: " . '../Login/Login.php' );
		exit;
	}

?>

<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>淨土念佛堂法會祭文與疏文列印主頁</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</HEAD>
<BODY>
	<div class="dataArea"><!-- style="width: 60%; margin: auto; border: 2px solid #00b300;" -->
		<div id="forDnld"><!-- for download into the main PaiWei Admin Page -->
		<h2 style="margin-top: 4vh; text-align: center; letter-spacing: 20px;">
			下載列印祭文與疏文
		</h2>		
		<form action="dnldJiWenPDF.php" method="post" id="dnldJiWen" style="font-weight:bold; padding: 10px;">
			<table class="dialog">
				<thead><th>法會祭、疏文類別</th><th>下載列印指令</th></thead>
				<tbody>
					<tr>
						<td>
							<select name="rtEvent" style="font-size: 1.2em;" required>
								<option value="">-- 請選擇法會祭、疏文類別 --</option>
								<option value="Qingming">清明祭祖法會</option>
								<option value="Zhongyuan">中元祭祖法會</option>
								<option value="ThriceYearning">三時繫念佛事法會</option>
							</select>
						</td>
						<td style="text-align: center; vertical-align: middle; padding: 1vh 0px;">
							<input type="submit" value="下  載" name="submit">
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		</div>
	</div>
</BODY>
<HTML>