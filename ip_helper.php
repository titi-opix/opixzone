<?php
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function is_allowed_ip($kon, $current_ip) {
    $query = mysqli_query($kon, "SELECT * FROM allowed_ips");
    while($row = mysqli_fetch_array($query)){
        if(is_same_subnet($current_ip, $row['ip_address'])){
            return true;
        }
    }
    return false;
}

function is_same_subnet($ip1, $ip2) {
    // Handle localhost matching localhost ONLY
    if (($ip1 == '::1' || $ip1 == '127.0.0.1') && ($ip2 == '::1' || $ip2 == '127.0.0.1')) {
        return true;
    }
    
    // If user is localhost but room is NOT localhost -> Fail
    if ($ip1 == '::1' || $ip1 == '127.0.0.1') {
        return false;
    }

    $parts1 = explode('.', $ip1);
    $parts2 = explode('.', $ip2);

    // Check if both are IPv4 (4 parts)
    if (count($parts1) == 4 && count($parts2) == 4) {
        // Compare first 3 parts (Class C /24 subnet)
        if ($parts1[0] == $parts2[0] && $parts1[1] == $parts2[1] && $parts1[2] == $parts2[2]) {
            return true;
        }
    }
    return false;
}
?>
