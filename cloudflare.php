<?php
/**
 * restore original visitor IP with Nginx
 * auto update IPS script
 * Author: Harry Tang <harry@modernkernel.com>
 */

$ips=[];

$sources['v4']=file_get_contents('https://www.cloudflare.com/ips-v4');
$sources['v6']=file_get_contents('https://www.cloudflare.com/ips-v6');

foreach($sources as $v=>$data){
	if(!empty($data)){
		$lines = explode("\n", $data);
		foreach ($lines as $ip) {
			if(!empty($ip)){		
				$ips[]=$ip;
			}
		} 	
	}
}

if(!empty($ips)){
	$conf='';
	foreach($ips as $ip){
		$conf.="set_real_ip_from $ip;\n";
	}
	$conf.="real_ip_header CF-Connecting-IP;\n";
	file_put_contents('/etc/nginx/conf.d/cloudflare.conf', $conf);
	shell_exec('service nginx reload');
	echo $conf;
}
