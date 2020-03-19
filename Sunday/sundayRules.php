<?php
/**********************************************************
 *    Sunday Qifu & Merit Dedication Request Rules        *
 **********************************************************/

	require_once( '../pgConstants.php' );
	require_once( 'dbSetup.php' );
	require_once( 'ChkTimeOut.php' );

	function xLate( $what ) {
		global $sessLang;
		$htmlNames = array (
			'htmlTitle' => array (
				SESS_LANG_CHN => "淨土念佛堂週日早課祈福及回向申請主頁",
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

	if ( (! isset( $_SESSION[ 'byPass' ] )) || $_SESSION[ 'byPass' ] == false) {
		if ( !isset( $_SESSION[ 'usrName' ] ) ) {
			header( "location: " . $hdrURL );
		} // redirect
	}
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
		<h2>申請要求與辦法</h2>
		<dl>
			<dt>親自出席</dt><br/>
			<dd>申請人必須親自參加早課或供佛典禮，或有指定的代表在場。
				如有緊急狀況 <b>(僅限本館同修)</b>，可請法務組到場義工作為代表，否則恕不受理。</dd><br/>
			<dt>遵守時限</dt><br/>
			<dd>無論是申請祈福與回向 (請見下列時限) 或是擔任功德主，均以不妨礙佛堂早課準時開始為原則。</dd><br/>
			<dt>申請時限</dt><br/>
			<dd>
				<table class="sundayRule">
					<thead>
						<tr><th colspan="4">
							祈&nbsp;福&nbsp;或&nbsp;回&nbsp;向&nbsp;申&nbsp;請&nbsp;辦&nbsp;法&nbsp;與&nbsp;時&nbsp;限
							</th>
						</tr>
						<tr><th style="width: 8vw;">狀&nbsp;&nbsp;&nbsp;&nbsp;況</th>
							<th style="witdh: 70vw;">説&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;明</th>
							<th style="width: 15vw;">申&nbsp;&nbsp;請&nbsp;&nbsp;辦&nbsp;&nbsp;法</th>
							<th style="width: 15vw">截&nbsp;止&nbsp;時&nbsp;間<br/>(逾時請恕無法受理)</th></tr>
					</thead>
					<tbody>
						<tr>
							<td style="text-align: center; font-weight:bold;">一般狀況</td><td>星期天早晨申請截止之前，即已經知道有家人或親友，需要祈福或回向</td>
							<td>在本網頁直接填寫申請</td><td>平常星期天早晨 9:00；<br/>供佛日星期天早晨 8:30</td>
						</tr>
						<tr>
							<td style="text-align: center; font-weight: bold;">突發狀況</td><td>星期天早晨申請截止之後，才知道有家人或親友，需要祈福或回向</td>
							<td>傳真、簡訊、或現場填寫</td><td>平常星期天早晨 9:45；<br/>供佛日星期天早晨 9:15</td>
							<!-- <td>星期日早課開始 15 分鐘之前</td> -->
						</tr>
						<tr>
							<td colspan="4" style="text-align:center; font-weight:bold;">
								<ul>
									<li>傳真：(630) 428-9961</li>
									<li>簡訊 1: (312) 907-1652</li>
									<li>簡訊 2: (630) 846-6844</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</dd><br/>
			<dt>申請祈福與回向的次數</dt><br/>
			<dd>
				申請時，請註明每次明確的日期。若希望祈福或回向多次者:<br/><br/>
				<ul style="list-style-type:square;">
					<li><b>祈福</b>:&nbsp;最多以三次為限</li>
					<li><b>回向</b>:&nbsp;
						<ol type="A">
						<li>一般回向，或申請者本人無法出席，而由代表出席者，<b>最多以三次為限</b>；</li>
						<li>申請為親人做七七之內的七次回向者，需為經常參加本館共修之同修；</li>
						<li>申請做七次回向者，除非有特殊狀況，並經過館長許可，申請人一定必須每次都在場參加，否則即依上述 "A" 的代理回向處理。</li>
						<li>如果往生者已超過七七，則回向<b>以一次為限</b>。
						</ol>
				</ul>
			</dd><br/>
			<dt>申請做功德主</dt><br/>
			<dd>
				同修若要申請做功德主，請先送 email 到佛堂 (library@amitabhalibrary.org)，直接向副館長申請。
				經確認後，請務必於佛堂早課開始前 <b>10分鐘</b> 到達佛堂練習。未經確認或練習者，恕不受理。
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
							<td>Situation known before the Application Deadline on Sundays</td>
							<td>Submit request via this webpage</td>
							<td>9:00am on regular Sundays;<br/>8:30am on Sundays w/ Buddha Offering Ceremony<br/></td>
						</tr>
						<tr>
							<td style="text-align: center; font-weight: bold;">Urgent Scenario</td>
							<td>Situation learned after the Application Deadline on Sundays</td>
							<td>Submit via fax, text, or on-site</td><td>15 minutes before respective Sunday Activity starts</td>
						</tr>
						<tr>
							<td colspan="4" style="text-align:center; font-weight:bold;">
								<ul>
									<li>Fax：(630) 428-9961</li>
									<li>Text Option 1: (312) 907-1652</li>
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
				For Well-wishing, the requests are limited to three times.
				For Merit-dedication and you cannot be present, the requests are limited to three times;
				for Merit-dedication and the deceased has been beyond 49 days, the request is limited to the immediate applicable Sunday only.
			</dd><br/>
			<dt>Request to Serve as a Sponsor</dt>
			<dd>				
				If you want to request to serve as a sponsor, please send email to us (library@amitabhalibrary.org).
				If you reveive a confirmation email, please arrive at the Pure Land Center at least <b>10 minutes</b> before the Sunday
				activity starts for training. Otherwise, your request will not be granted.				
			</dd>
		</dl><!-- End of dl -->        
<?php
	} // English version
?>
        </div><!-- END for loading into the #tabDataFrame in sunday/index.php -->
    </div><!-- DataArea -->
</body>
</html>