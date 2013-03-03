<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="generator" content="HTML Tidy for Windows (vers 25 March 2009), see www.w3.org" />

  <title>Monster ID
  </title>
</head>

<?php 
function validip($ip) { 
    if (!empty($ip) && ip2long($ip)!=-1) { 
        $reserved_ips = array ( 
            array('0.0.0.0','2.255.255.255'), 
            array('10.0.0.0','10.255.255.255'), 
            array('127.0.0.0','127.255.255.255'), 
            array('169.254.0.0','169.254.255.255'), 
            array('172.16.0.0','172.31.255.255'),     
            array('192.0.2.0','192.0.2.255'), 
            array('192.168.0.0','192.168.255.255'), 
            array('255.255.255.0','255.255.255.255') 
        );
  
        foreach ($reserved_ips as $r) { 
            $min = ip2long($r[0]); 
            $max = ip2long($r[1]); 
            if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false; 
        } 
        return true; 
    }
    else { 
        return false; 
    }
}
 
function getip() { 
    if (validip($_SERVER["HTTP_CLIENT_IP"])) { 
        return $_SERVER["HTTP_CLIENT_IP"]; 
    }
    foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) { 
        if (validip(trim($ip))) { 
            return $ip; 
        } 
    }   
 
    if (validip($_SERVER["HTTP_X_FORWARDED"])) { 
        return $_SERVER["HTTP_X_FORWARDED"]; 
    } elseif (validip($_SERVER["HTTP_FORWARDED_FOR"])) { 
        return $_SERVER["HTTP_FORWARDED_FOR"]; 
    } elseif (validip($_SERVER["HTTP_FORWARDED"])) { 
        return $_SERVER["HTTP_FORWARDED"]; 
    } elseif (validip($_SERVER["HTTP_X_FORWARDED"])) { 
        return $_SERVER["HTTP_X_FORWARDED"]; 
    } else { 
        return $_SERVER["REMOTE_ADDR"]; 
    }
}
?>

<body>
<img src="monsterid.php?seed=<?php print getip();?>" />
<?php 
for ( $counter = 1; $counter <= 31; $counter += 1) {
    $seed = rand();
    echo "<img src=\"monsterid.php?seed=$seed\" />";
}
?>
</body>
</html>
