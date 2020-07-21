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
				SESS_LANG_CHN => "淨土念佛堂結緣法寶申請主頁",
				SESS_LANG_ENG => "Dharma Items Application Page" )
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
		<div class="dharmaItemsRuleTxt">
			<div class="dharmaItemsAppreciate">
				<b>所有結緣法寶，均由十方善信大德捐助<br/>請惜福惜緣，尊重法寶</b><br/><br/>
				<span style="font-size: 0.9em;">歡迎助印及贊助郵費，捐助法寶流通，共植福田！</span>
			</div>
			<div class="bulletItem">
				<dl>
					<dt>確實使用</dt>
					<dd>只申請您所需要的法寶，申請之後確實使用。</dd><br/>
					<dt>法寶不回收</dt>
					<dd>本館所有服務均由義工發心供養，資源確實有限，無法回收任何法寶；<br/>
						向本館申請發放的法寶，也同樣無法回收處理，敬請見諒。</dd><br/>
					<dt>就近申請</dt>
					<dd>申請法寶請依地緣方便，先就近向各地淨宗學會或道場等機構申請。請參攷
						<a href="http://www.amtb.org.tw/jzplace.htm" target="_blank">世界淨宗聯絡處</a>。
					</dd><br/>
					<dt>美國地區同修申請法寶</dt>
					<dd>由於本館義工有限，郵寄法寶原則上以美國本地為限。居住在大芝加哥地區，至本館單程車程在一小時以內的同修朋友，
						請您儘量親至本館領取結緣法寶。
					</dd><br/>
					<dt>亞洲及世界其他地區同修申請法寶</dt>
					<dd>居住在亞洲及世界其他地區之學佛同修可直接以傳真或電郵向下列兩個學會申請<br/><br/>
						<table class="plcOrgs">
							<thead>
								<th>佛陀教育基金會</th>
								<th>社團法人中華華藏淨宗學會</th>
							</thead>
							<tbody>
								<td>
									佛陀教育基金會<br/>
									台北市杭州南路 1 段 55 號 11 樓，郵遞區號 100<br/>
									Taipei, Taiwan, R.O.C.<br/>
									Fax: (886)-2-2391-3415<br/>
									Website: <a href="http://www.budaedu.org">http://www.budaedu.org</a><br/>
									Email: <a href="mailto:budaedu@budaedu.org">budaedu@budaedu.org</a><br/>
								</td>
								<td>
									社團法人中華華藏淨宗學會<br/>
									<a href="mailto:hwadzan@hwadzan.com">法寶申請專用信箱</a><br/>
									Fax： (886)-2-2754-7262<br/>
									Website：<a href="http://www.hwadzan.org.tw">http://www.hwadzan.org.tw</a> 或<br/>
									<a href="http://www.hwadzan.net">http://www.hwadzan.net</a><br/>
									Email： <a href="mailto:amtb@hwadzan.com">amtb@hwadzan.com</a>
								</td>
							</tbody>
						</table>
					</dd><br/>
					<dt>填寫申請表</dt>
					<dd>所有法寶申請需由申請人、團體負責人、或活動主辦人先填具申請表。
						團體或活動申請法寶，必須說明用途或活動性質及所需法寶數量。
					</dd><br/>
					<dt>限量申請</dt>
					<dd>為便利流通，避免浪費，個人申請法寶每次總數以五項或五冊為限；若所請法寶為成套者，以一套為限。
						若有特殊情形，需要較多的法寶，請與本館書目組聯絡，本館會酌情處理。
						團體或活動申請法寶，將依用途及活動性質，以個案處理。</dd>
				</dl>
			</div><!-- bulletItem -->
		</div><!-- Rule Text -->
   
<?php
	} else { // English version
?>
		<div class="dharmaItemsRuleTxt">
			<div class="dharmaItemsAppreciate">
				<b>All Dharma items are sponsored by Dharma friends worldwide.<br/>
				Please appreciate and take good care of them when you have them.</b><br/><br/>
				<span style="font-size: 0.9em;">All sponsorships are warmly welcomed and appreciated.</span>
			</div>
			<div class="bulletItem">
				<dl>
					<dt>Apply What You Need</dt>
					<dd>Apply ONLY what you need; ACTUALLY USE what you get.</dd><br/>
					<dt>We do not recycle Dharma items</dt>
                	<dd>All services in our center are offered by solunteers; there are very limited resources.
						Therefore, we <b>CANNOT</b> recycle any Dharma items, including those distributed by our center.
					</dd><br/>
					<dt>Apply for Dharma items from centers nearby</dt>
            	    <dd>Please apply for Dharma items from locations and organizations nearby you.
                    	In case you cannot find one,<br/>please <a href="mailto:books@amitabhalibrary.org">write to us</a>.
					</dd><br/>
					<dt>For Dharma friends in the United States</dt>
					<dd>Due to very limited resources, we only mail Dharma items, if feasible, to the
						address within the United States. If you are located in the Greater Chicago Area and
						within one-hour driving distance, we ask that you stop by our center and pick up the
						requested items.
					</dd><br/>
					<dt>For Dharma friends in other areas of the world</dt>
                	<dd>If you are located outside of the United States, please contact either of the below
						organizations via fax or email<br/><br/>
						<table class="plcOrgs" style="width: 98%;">
							<thead>
								<th width="55%;">The Corporate Body of<br/>The Buddha Education Foundation</th>
								<th>The Corporate Republic of Hwa Dzan Society</th>
							</thead>
							<tbody>
								<td>
									The Corporate Body of The Buddha Education Foundation<br/>
									No. 55, Section 1, Hangzhou South Road, Zhongzheng District<br/>
									Taipei City, Taiwan, Republic of China 100<br/>
									Fax: (886)-2-2391-3415<br/>
									Website: <a href="http://www.budaedu.org">http://www.budaedu.org</a><br/>
									Email: <a href="mailto:budaedu@budaedu.org">budaedu@budaedu.org</a><br/>
								</td>
								<td>
									The Corporate Republic of Hwa Dzan Society<br/>
									<a href="mailto:hwadzan@hwadzan.com">Email Dedicated to Dharma Item Requests</a><br/>
									Fax： (886)-2-2754-7262<br/>
									Website：<a href="http://www.hwadzan.org.tw">http://www.hwadzan.org.tw</a> or<br/>
									<a href="http://www.hwadzan.net">http://www.hwadzan.net</a><br/>
									Email： <a href="mailto:amtb@hwadzan.com">amtb@hwadzan.com</a>
								</td>
							</tbody>
						</table>
					</dd><br/>

					<dt>Submit an Application Form</dt>
					<dd>An Application Form must be submitted by an individual applicant, a group in-charge, or
						an activity host.<br/>
						If it is for a group or an activity, please state clearly the use and quantity of
						the requested items.
					</dd><br/>
					<dt>Quantity Limitation</dt>
					<dd>To avoid waste and facilitate Dharm items circulation, each application is limited to
						five items in total. If the requested items come in sets, then the limit is one.
						If you have special circumstances and need more than the allowed amount, please contact
						the cataloging team and the center will assess your situation and determine if we can serve you.
					</dd>
				</dl>
			</div><!-- bulletItem -->
		</div><!-- Rule Text -->
    
<?php
	} // English version
?>
        </div><!-- END 'ruleText' for loading into the #tabDataFrame in DharmaItems/index.php -->
    </div><!-- DataArea -->
</body>
</html>