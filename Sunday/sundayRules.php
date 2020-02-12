<?php
/**********************************************************
 *    Sunday Qifu & Merit Dedication Request Rules        *
 **********************************************************/
/*
 * To fit into the PaiWei SW architecture, the '#ugDesc' <div> will be loaded by the PaiWei.js
 */ 
	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂週日祈福及迴向申請主頁",
				SESS_LANG_ENG => "Sunday Well-wishing &amp; Merit Dedication Application Page" )
		);
		return $htmlNames[ $what ][ $sessLang ];
	} // xLate()

	$sessLang = SESS_LANG_CHN; // default
	if ( isset( $_SESSION[ 'sessLang' ] ) ) {
		$sessLang = $_SESSION[ 'sessLang' ];
	}	
	$_SESSION[ 'sessLang' ] = $sessLang;

	$hdrURL = URL_ROOT . "/admin/index.php";
	$useChn = ( $sessLang == SESS_LANG_CHN );
	if ( !isset( $_SESSION[ 'usrName' ] ) ) {
		header( "location: " . $hdrURL );
	} // redirect
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo xLate( 'htmlTitle' ); ?></title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../master.css">
<link rel="stylesheet" type="text/css" href="./sundayRules.css">
</head>
<body>
	<div class="dataArea" style="overflow-y: auto;">
		<div id="ruleText"><!-- BEGIN for loading into the #tabDataFrame in sunday/index.php -->
<?php
	if ( $useChn ) { // Chinese version
 ?>
		<!-- <h2>申請要求與辦法</h2> -->
		<dl>
			<dt>親自出席</dt>
			<dd>申請人必須親自參加早課或供佛典禮，或有指定的代表在場。
				如有緊急狀況 <b>(僅限本館同修)</b>，可請法務組義工為代表，否則恕不受理。</dd><br/>
			<dt>遵守時限</dt>
			<dd>無論是申請陽上祈福與往生迴向 (請見下列時限) 或是擔任功德主，均以不妨礙佛堂早課準時開始為原則。</dd><br/>
			<dt>申請時限</dt>
			<dd>
				<table class="sundayRule">
					<thead>
						<tr><th colspan="4">
							陽&nbsp;上&nbsp;祈&nbsp;福&nbsp;或&nbsp;往&nbsp;生&nbsp;迴&nbsp;向&nbsp;申&nbsp;請&nbsp;辦&nbsp;法&nbsp;與&nbsp;時&nbsp;限
							</th>
						</tr>
						<tr><th style="width: 8vw;">狀&nbsp;&nbsp;&nbsp;&nbsp;況</th>
							<th style="witdh: 70vw;">説&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;明</th>
							<th style="width: 15vw;">申&nbsp;&nbsp;請&nbsp;&nbsp;辦&nbsp;&nbsp;法</th>
							<th style="width: 15vw">截&nbsp;止&nbsp;時&nbsp;間<br/>(逾時請恕無法受理)</th></tr>
					</thead>
					<tbody>
						<tr>
							<td style="text-align: center; font-weight:bold;">一般狀況</td><td>星期日早晨申請截止之前即已經知道親友或家人需要陽上祈福或往生迴向</td>
							<td>由本網頁直接申請</td><td>平常星期日早晨 9:00；<br/>供佛日星期日早晨 8:30</td>
						</tr>
						<tr>
							<td style="text-align: center; font-weight: bold;">突發狀況</td><td>星期日早晨申請截止之後才知道親友或家人需要陽上祈福或往生迴向</td>
							<td>傳真、簡訊、或現場填寫</td><td>平常星期日早晨 9:45；<br/>供佛日星期日早晨 9:15</td>
							<!-- <td>星期日早課開始 15 分鐘之前</td> -->
						</tr>
						<tr>
							<td colspan="4" style="text-align:center; font-weight:bold;">
								<ul>
									<li>傳真：(630) 428-9961</li>
									<li>簡訊 1: TBA</li>
									<li>簡訊 2: (630) 846-6844</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</dd><br/>
			<dt>陽上祈福與往生迴向的次數</dt>
			<dd>
				若希望陽上祈福或往生迴向多次者，申請時請註明明確之日期。<b>陽上祈福以三次為限</b>。往生迴向若本人無法出席而由代表出席者，<b>以三次為限</b>；<br/>
				往生迴向往生者若已超過七七，<b>以一次為限</b>。
			</dd><br/>
			<dt>申請做功德主</dt>
			<dd>
				申請做功德主，辦法與上同，但請務必儘早與法務組聯絡，提早10分鐘到佛堂練習。
				未經練習者，恕不受理。
			</dd>
		</dl><!-- End of dl -->        
<?php
	} else { // English version
?>
		<h2 style="margin-top: 0px; text-align: center; letter-spacing: normal; color: blue;">
            Requirements and Prodedures
        </h2>
		<dl>
			<dt>Be Present</dt>
			<dd>The requestor shall be present in the Sunday activities during which the well-wish and/or merit dedication
				will be requested. If you have an undue situation, you (limited to regular participants / practitioners )
				must designate a representative who will be present in the activities to represent you. Otherwise the
				application will not be accepted.
			</dd><br/>
			<dt>Observe the Application Deadline</dt>
			<dd>Requests shall be submitted in time so as to NOT IMPACT the scheduled start times of the respective activities.
			</dd><br/>
			<dt>Application Deadlines</dt>
			<dd>
				<table class="sundayRule">
					<thead>
						<tr>
							<th colspan="4">
								Well-wishing &amp; Merit Dedication Request Deadlines and Methods
							</th>
						</tr>
						<tr><th style="width: 8vw;">Scenario</th>
							<th style="witdh: 70vw;">Description</th>
							<th style="width: 15vw;">Request Method</th>
							<th style="width: 15vw">Deadlines</th></tr>
					</thead>
					<tbody>
						<tr>
							<td style="text-align: center; font-weight:bold;">Normal Scenario</td>
							<td>Situation known before 8:00am on Sunday</td>
							<td>Submit request via this webpage online</td><td>8:00am on Sunday</td>
						</tr>
						<tr>
							<td style="text-align: center; font-weight: bold;">Urgent Scenario</td>
							<td>Situation learned after 8:00am on Sunday</td>
							<td>Submit via fax, text, or on-site</td><td>15 minutes before Sunday Activity starts</td>
						</tr>
						<tr>
							<td colspan="4" style="text-align:center; font-weight:bold;">
								<ul>
									<li>Fax：(630) 428-9961;</li>
									<li>Text Option 1: (630) 721-1130;</li>
									<li>Text Option 2: (630) 846-6844</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</dd><br/>
			<dt>Multi-Sunday Requests</dt>
			<dd>
				When submitting requests, please indicate clearly the Sunday(s) on which your requests will be applied.
				If you will be represented by designees, then you cannot request for more than three Sundays.
			</dd><br/>
			<dt>Request to Serve as a Sponsor</dt>
			<dd>
				If you want to request to serve as a sponsor, the same procedures and methods apply.
				In addition, you must contact the volunteer in-charge for Dharma Activities.
				If you never served such a role in a retreat, you must be trained before your request can be accepted.
				<b>(Please contact the Dharma-Activity in-charge to arrange the training.)</b>
			</dd>
		</dl><!-- End of dl -->        
<?php
	} // English version
?>
        </div><!-- END for loading into the #tabDataFrame in sunday/index.php -->
    </div><!-- DataArea -->
</body>
</html>