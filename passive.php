<html>
<head>
<title>Passive DNS</title>
<style type="text/css">
<!--
A:link {text-decoration: none}
A:visited {text-decoration: none}
A:active {text-decoration: none}
A:hover {text-decoration: underline}
-->
</style>
</head>
<body>
<?php

// Passive DNS Php Frontend ..quick and dirty..maade for Dtrackr.com by Chris Clark
// chris@xenosec.org / #xenosec / xen0ph0n @ github.com
// Copyright and Licenced GPL v3

// dbconnection
mysql_connect("MYSQLHOST", "MYSQLUSER", "MYSQLPASS") or die(mysql_error());
mysql_select_db("MYSQLDB") or die(mysql_error());

function is_valid_domain_name($domain_name)
{
    $pieces = explode(".",$domain_name);
    foreach($pieces as $piece)
    {
        if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $piece)
            || preg_match('/-$/', $piece) )
        {
            return false;
        }
    }
    return true;
}

if (!isset($_GET['query'])){
echo "<b>Passive DNS</b><br><br>";
echo "Please Enter Domain or IP to query passive DNS records: "; 
echo '<form name search method="get">';
echo 'Domain/IP: <input type="text" maxlength="100" name="query">';
echo '<input type="submit" value="Track"></form>';
}


else {
$query = $_GET['query'];

//check validity of input and check if either IP or Domain
if(filter_var($query, FILTER_VALIDATE_IP)){
echo "<b>Passive DNS Records for IP: ". $query ." below:</b><br><br>";
$domains = mysql_query("SELECT * FROM pdns WHERE answer='$query'");
if(mysql_num_rows($domains)==0){
echo "<b>Zero Results in the local PDNS Database</b><br><br>";
echo "Please Enter A New Domain or IP: ";
echo '<form name search method="get">';
echo 'Domain/IP: <input type="text" maxlength="100" name="query">';
echo '<input type="submit" value="Track"></form>';
}
else{
 echo "<table cellpadding='5'><tr><td>Domain</td><td>Type</td><td>Result</td><td>TTL</td><td>First Seen</td><td>Last Seen</td></tr>";
while($row = mysql_fetch_array($domains)){
 echo  "<tr><td><a href='passive.php?query=". $row['QUERY'] . "'>". $row['QUERY']."</a></td><td>". $row['MAPTYPE']."</td><td>". $row['ANSWER']."</a></td><td>". $row['TTL']."</td><td>". $row['FIRST_SEEN']."</td><td>". $row['LAST_SEEN'] ."</td><td></tr>";
  
}
echo "</table>";
echo '<form name search method="get">';
echo '<br><b>New Domain/IP: <input type="text" maxlength="100" name="query">';
echo '<input type="submit" value="Track"></form>';

}
}
elseif(is_valid_domain_name($query)){
echo "<b>Passive DNS Records for Domain: ". $query ." below:</b> <br><br>";
$ips = mysql_query("SELECT * FROM pdns WHERE query='$query'");
if(mysql_num_rows($ips)==0){
echo "<b>Zero Results in the local PDNS Database</b><br><br>";
echo "Please Enter A New Domain or IP: ";
echo '<form name search method="get">';
echo 'New Domain/IP: <input type="text" maxlength="100" name="query">';
echo '<input type="submit" value="Track"></form>';
}
else{ 

 echo "<table cellpadding='5'><tr><td>Domain</td><td>Type</td><td>Result</td><td>TTL</td><td>First Seen</td><td>Last Seen</td></tr>";
while($row = mysql_fetch_array($ips)){
 echo  "<tr><td>". $row['QUERY']."</td><td>". $row['MAPTYPE']."</td><td><a href='passive.php?query=". $row['ANSWER'] . "'>". $row['ANSWER']."</a></td><td>". $row['TTL']."</td><td>". $row['FIRST_SEEN']."</td><td>". $row['LAST_SEEN'] ."</td><td></tr>";
	
}

echo "</table>";
echo '<form name search method="get">';
echo '<br><b>Domain/IP: <input type="text" maxlength="100" name="query">';
echo '<input type="submit" value="Track"></form>';

}
}
else{
echo "<b>You failed to enter a valid IP or Domain!</b><br><br>";
echo "Please Enter Domain or IP to query passive DNS records: ";
echo '<form name search method="get">';
echo 'Domain/IP: <input type="text" maxlength="100" name="query">';
echo '<input type="submit" value="Track"></form>';
}

}


?>
</body>
</html>
