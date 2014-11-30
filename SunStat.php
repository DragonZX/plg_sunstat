<?php
# @version		$version 0.3 Amvis United Company Limited  $
# @copyright	Copyright (C) 2014 AUnited Co Ltd. All rights reserved.
# @license		GNU/GPL, see LICENSE.php
# Updated		16th May 2014
#
# Site: http://aunited.ru
# Email: info@aunited.ru
# Phone
#
# Joomla! is free software. This version may have been modified pursuant
# to the GNU General Public License, and as distributed it includes or
# is derivative of works licensed under the GNU General Public License or
# other free or open source software licenses.
# See COPYRIGHT.php for copyright notices and details.

#Prefixes
# mr_ = MailRu
# ym_ = Yandex
# ga_ = Google
# pwk_ = Piwik
# li_ = LiveInternet
# hl_ = Hotlog

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');

class plgSystemSunStat extends JPlugin
{
	function plgSunStat(&$subject, $config)
	{		
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin( 'system', 'SunStat' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	function onAfterRender()
	{
        $app = JFactory::getApplication();
        if($app->isAdmin())
        {
            return;
        }
		// Initialise variables
		$noIndex = "";
		//mailru
		$mr_enabled 			= $this->params->get( 'mr_enabled', '' );
		$mr_noIndexWrapper 		= $this->params->get( 'mr_noindexWrapper', '1' );
		$mr_id 					= $this->params->get( 'mr_id', '' );
		//yandex metrika
		$ym_enabled 			= $this->params->get( 'ym_enabled', '' );
		$ym_noIndexWrapper 		= $this->params->get( 'ym_noindexWrapper', '1' );
		$ym_id 					= $this->params->get( 'ym_id', '' );
        $ym_webvisor 			= $this->params->get( 'ym_webvisor', '' );
		$ym_clickMap 			= $this->params->get( 'ym_clickMap', '' );
        $ym_linksOut 			= $this->params->get( 'ym_linksOut', '' );
        $ym_accurateTrackBounce = $this->params->get( 'ym_accurateTrackBounce', '' );
        $ym_noIndex 			= $this->params->get( 'ym_noIndex', '' );
		//google analytics
		$ga_enabled 			= $this->params->get( 'ga_enabled', '' );
		$ga_legacy	 			= $this->params->get( 'ga_legacy', '' );
		$ga_noIndexWrapper 		= $this->params->get( 'ga_noindexWrapper', '1' );
		$ga_id 					= $this->params->get( 'ga_id', '' );
		$ga_domain 				= $this->params->get( 'ga_domain', '' );
		$ga_uid 				= $this->params->get( 'ga_uid', '' );
		$ga_demographic			= $this->params->get( 'ga_demographic', '' );
		$ga_extattrib			= $this->params->get( 'ga_extattrib', '' );
		//piwik
		$pwk_enabled 			= $this->params->get( 'pwk_enabled', '' );
		$pwk_noIndexWrapper 	= $this->params->get( 'pwk_noindexWrapper', '1' );
		$pwk_addDomain			= $this->params->get( 'pwk_addDomain', '' );
		$pwk_subdomains 		= $this->params->get( 'pwk_subdomains', '' );
		$pwk_linksOut 			= $this->params->get( 'pwk_linksOut', '' );
		$pwk_address 			= $this->params->get( 'pwk_address', '' );
		$pwk_domain 			= $this->params->get( 'pwk_domain', '' );
		//liveinternet
		$li_enabled 			= $this->params->get( 'li_enabled', '' );
		$li_noIndexWrapper 		= $this->params->get( 'li_noindexWrapper', '1' );
		//hotlog
		$hl_enabled 			= $this->params->get( 'hl_enabled', '' );
		$hl_noIndexWrapper 		= $this->params->get( 'hl_noindexWrapper', '1' );
		$hl_id 					= $this->params->get( 'hl_id', '' );
		
		//getting body code and storing as buffer
		$buffer = JResponse::getBody();
		
		//Extended functions
		if ($ga_demographic)$ga_ef_demographic	="ga('require', 'displayfeatures');"; 										 else $ga_ef_demographic='';
		if ($ga_extattrib) 	$ga_ef_extattrib	="ga('require', 'linkid', 'linkid.js');";									 else $ga_ef_extattrib='';
		if ($ga_uid) 		$ga_ef_uid			='ga(‘set’, ‘&uid’, {{USER_ID}});'; 										 else $ga_ef_uid='';
		//Google legacy
		if ($ga_demographic)$gal_ef_demographic	="ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';"; 										 else $gal_ef_demographic="ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';";
		if ($ga_extattrib) 	$gal_ef_extattrib	="var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js'; _gaq.push(['_require', 'inpage_linkid', pluginUrl]);";						else $gal_ef_extattrib='';
		//Piwik
		if ($pwk_linksOut) 	$pwk_ef_linksOut	='_paq.push(["setDomains", ["*.'.$pwk_domain.'"]]); '; 						 else $pwk_ef_linksOut='';
		if ($pwk_subdomains)$pwk_ef_subdomains	='_paq.push(["setCookieDomain", "*.'.$pwk_domain.'"]); '; 					 else $pwk_ef_subdomains='';
		if ($pwk_addDomain)	$pwk_ef_addDomain	='_paq.push(["setDocumentTitle", document.domain + "/" + document.title]);'; else $pwk_ef_addDomain='';
		//embed code
		$noIndex = $noIndex ? 'ut:"noindex",' : '';

        $mr		= '<!-- Rating@Mail.ru counter --><script type="text/javascript">//<![CDATA[var _tmr = _tmr || [];_tmr.push({id: '.$mr_id.', type: "pageView", start: (new Date()).getTime()});(function (d, w) {   var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true;   ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";   var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};   if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }})(document, window);//]]></script><noscript><div style="position:absolute;left:-10000px;"><img src="//top-fwz1.mail.ru/counter?id='.$mr_id.';js=na" style="border:0;" height="1" width="1" alt="Rating@Mail.ru" /></noscript><!-- /Rating@Mail.ru counter -->';
		if($mr_noIndexWrapper) $mr = '<!--noindex-->' . $mr . '<!--/noindex-->';
		$ym		= '<!-- Yandex.Metrika counter --><script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter' . $ym_id . ' = new Ya.Metrika({id:' . $ym_id . ', clickmap:' . $ym_clickMap . ', trackLinks:' . $ym_linksOut . ', accurateTrackBounce:' . $ym_accurateTrackBounce  . ','. $ym_noIndex . ' webvisor:' . $ym_webvisor . '}); } catch(e) {} }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/' . $ym_id . '" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->';
		if($ym_noIndexWrapper) $ym = '<!--noindex-->' . $ym . '<!--/noindex-->';
		$ga		= "<!-- Universal Analytics counter --><script type='text/javascript'>  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)  })(window,document,'script','//www.google-analytics.com/analytics.js','ga'); ga('create', 'UA-".$ga_id."', '".$ga_domain."'); ".$ga_ef_demographic.$ga_ef_extattrib." ga('send', 'pageview'); ".$ga_ef_uid." </script><!-- /Universal Analytics counter -->";	
		if($ga_legacy) $ga = "<!-- Google Analytics counter --><script type='text/javascript'>  var _gaq = _gaq || [];".$ga_ef_extattrib.$ga_ef_uid." _gaq.push(['_setAccount', 'UA-".$ga_id."]);  _gaq.push(['_setDomainName', 'none']);  _gaq.push(['_setAllowLinker', true]);_gaq.push(['_addDevId', 'YogEE'],['_trackPageview']);  (function() {    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;"    .$gal_ef_demographic.   " var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);  })();</script><!-- /Google Analytics counter -->";
		if($ga_noIndexWrapper) $ga = '<!--noindex-->' . $ga . '<!--/noindex-->';
		$li		= '<!-- LiveInternet counter --><script type="text/javascript"><!--new Image().src = "//counter.yadro.ru/hit?r"+escape(document.referrer)+((typeof(screen)=="undefined")?"":";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+";"+Math.random();//--></script><!-- /LiveInternet counter -->';
		if($li_noIndexWrapper) $li = '<!--noindex-->' . $li . '<!--/noindex-->';
		$pwk	= '<!-- Piwik counter --><script type="text/javascript">  var _paq = _paq || [];'.$pwk_ef_addDomain.$pwk_ef_subdomains.$pwk_ef_linksOut.'_paq.push(["trackPageView"]);  _paq.push(["enableLinkTracking"]);  (function() {    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://'.$pwk_address.'/";    _paq.push(["setTrackerUrl", u+"piwik.php"]);    _paq.push(["setSiteId", 1]);    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);  })();</script><noscript><p><img src="http://'.$pwk_domain.'/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript><!-- /Piwik counter -->';
		if($pwk_noIndexWrapper) $pwk = '<!--noindex-->' . $pwk . '<!--/noindex-->';
		$hl		= "<!-- HotLog counter --><span id='hotlog_counter'></span><span id='hotlog_dyn'></span><script type='text/javascript'> var hot_s = document.createElement('script'); hot_s.type = 'text/javascript'; hot_s.async = true; hot_s.src = 'http://js.hotlog.ru/dcounter/" . $hl_id ."; hot_d = document.getElementById('hotlog_dyn');hot_d.appendChild(hot_s);</script><noscript><a href='http://click.hotlog.ru/?" . $hl_id ."' target='_blank'><img src='http://hit.hotlog.ru/cgi-bin/hotlog/count?s=" . $hl_id ."&amp;im=307' border='0' alt='HotLog'></a></noscript><!-- /HotLog counter -->";
		if($hl_noIndexWrapper) $hl = '<!--noindex-->' . $hl . '<!--/noindex-->';
		
		//is it enabled?
		$javascript='';
		$space='
		';
        if ($mr_enabled)	$javascript= $javascript.$space.$mr;
		if ($ym_enabled)	$javascript= $javascript.$space.$ym;
		if ($ga_enabled)	$javascript= $javascript.$space.$ga;
		if ($li_enabled)	$javascript= $javascript.$space.$li;
		if ($pwk_enabled)	$javascript= $javascript.$space.$pwk;
		if ($hl_enabled)	$javascript= $javascript.$space.$hl;

		$buffer = preg_replace ("/<\/body>/", $javascript."\n\n</body>", $buffer);
		
		//output the buffer
		JResponse::setBody($buffer);
		
		return true;
	}
}
?>
