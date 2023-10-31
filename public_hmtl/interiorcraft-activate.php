<?php

$servername     = "localhost";
$username       = "ct16146_regdb";
$password       = "StYf5ZL8";
$dbname         = "ct16146_regdb";

header('Content-Type: text/html; charset=utf-8');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error)
	die("MySQL connection failed: " . $conn->connect_error);

$conn->query('set names utf8');

CreateTables($conn);

if ($_GET['action'] == "activation")
	DoActivation($conn);

$conn->close();


function CreateTables($conn)
{	
	$sql = "CREATE TABLE IF NOT EXISTS SerialKeys (
				ID INT AUTO_INCREMENT PRIMARY KEY, 
				SerialNumber CHAR(20) UNIQUE,
				LicenseeID INT NOT NULL DEFAULT 0,
				ProductID INT NOT NULL DEFAULT 0,
				Disabled INT NOT NULL DEFAULT 0,
				Activations INT NOT NULL DEFAULT 0,
				MaxActivations INT NOT NULL DEFAULT 3,
				CustomerID INT) 
			DEFAULT CHARACTER SET utf8
			DEFAULT COLLATE utf8_general_ci";
	
	if (!$conn->query($sql))
		echo "Error creating table SerialKeys: " . $conn->error . "<br/>";
		
	$sql = "CREATE TABLE IF NOT EXISTS Customers (
				ID INT AUTO_INCREMENT PRIMARY KEY,
				FirstName VARCHAR(100) NOT NULL,
				LastName VARCHAR(100) NOT NULL,
				Email VARCHAR(100) NOT NULL,
				SendEmails INT NOT NULL DEFAULT 1)
			DEFAULT CHARACTER SET utf8
			DEFAULT COLLATE utf8_general_ci";
	
	if (!$conn->query($sql))
    	echo "Error creating table Customers: " . $conn->error . "<br/>";
}

function DoActivation($conn)
{
	$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQEAlWPqJlG3nyaQ605LK3HVfHGfu5phwxAB6d0clEeXD82Zcubu
WArlk8d0bklDQrlrIxxUcTKrZDDZ49KqKZnw7RA+mFsDQ0JzfKE0mXPQWINzsuVK
l8CG8XM2Fw48PpdP/Xgq0KYuTWMy7/Id/HCJapI5IBcE6oA9OgaJKDG2cR4+ZtIR
Q2YjJNxdGfR/yKsUWqBIV787uhpzmfbhyKoJF8V5c6iav+/SZyQDJ6Dnu/A8noES
eb5rr70aIEVgrblzXMWrhSIReXDwn/i3fyrFJLlCVvIrCVvJX500pDMSQLyjZsZq
c4gxjvidlknxjSpEJC2k2NG23+2STKCwvIxmSwIDAQABAoIBAHYtaBmQI3YfCB1c
/lIL5xpeuEGsSxIII7thUy5pw7KYrn8a+All9V8GNbDY/ABYtcw8qQAzWSoVCwkS
qdHnGZQveZUEynv2oW1CCV4rI/IhavFmOa/6ecWfonZyqG3LPVgCK7yK+a32f2EZ
Y2dDjzFjIxWjoBvx/n1cljvZt72bPXlJ8/crqeIMtxPEUWJqoS11Q2iGG736Ti7c
nlRkagoqtTPuAgtiMwtQmUwhDVpoIU3vyNyG1HJKC5+P8tsP2eipGfcU/H7Al6cL
4qyOgAotD0AQjhsfW3uaVnhj7rWFU8Ot+3rc0EVigqcEHtbBpQltwyb/SDU5MW8n
wtXmaQECgYEAzQjovErJhdjKBURi4BE7W6+4WH1T08iLwXCsMnm8NLIMw6Apd3xx
tLqsQoohuAuZqTwy0PYVmLOvH1A/g5ZMuJpEmG84a0mgmEMnRRQkCAUJ0t3EAVsV
escZ7v983S2JBh+4Be0AsMVssoguuuznfouR6gkom1/3GXGldVdGyKECgYEAuoYl
8Xoa12rkGWCfcb28sfis9FkFAzrdduGPWuengIoKfB5rD3c4hOhbNVfmhOZU5fSA
WhBfAfzfMtb/UF3TX6SvKWdprk5TG78u5yC8IljhYU5SBVv2cEb9NTxzfbG/SoN3
tlADd9lGWl4MA+jYbjM1sLbhTfZs/PO+clYTq2sCgYAJEQXJnbz/kDCOvxFoOxrW
2RRbxV75lHqpmSPkL4HlCqKJ3AE5aWVVypNnddg73Td0Rlcw6lDWKcvqpI/Kb9EA
sMGrw+9Ivz68vOt1oIhfWmmuy2Opc6+leDxrVxzcYvEWNjza9jn7lx9RXbhDR8qM
y/st0C3dgQbaNy5L3AojQQKBgFDJ/fFDtBCOCdI8GZOIXQyw6yjCzfsCKh5twEvd
fp2cLMJp4nFGcEKQ6cCHHB+ALFGVv2pDIQOZZYt9uAQ1P3JzwDKsygdngXPJMSWG
9jXiTWx6IeiUVn93IAFI97T/oh1CKD3dFkN45pIJniarWeMRZzXtYFUGYVqGsHyR
b9+VAoGACqS85zxoPgdFCgQLvvPQaDoIc4XbOoVVfEak8tWLdUGHLdbSqugTjJUh
RfG8YwoIREC4UVZwc27GJz8psg3ob8uJO7TYRyviZBNALekb+8wO2Fs1iMTU0Ew6
Dmz/+47wZHQOr0ub1e8skv6w//N9ofGKmdwN1Cp08sBH+Aj7+xU=
-----END RSA PRIVATE KEY-----
EOD;

	$productid = $_GET['productid'];
	$key = $_GET['key'];
	$firstname = $_GET['firstname'];
	$lastname = $_GET['lastname'];
	$email = $_GET['email'];
	$sendemails = $_GET['sendemails'];
	$uniqueid = $_GET['uniqueid'];

	// echo "First name: " . $firstname . "<br/>";

	if (!$key || strlen($key) != 20)
	{
		//echo "Key length is incorrect";
		return;
	}
	
	if (is_null($firstname) || is_null($lastname) || is_null($email) || !$uniqueid || is_null($productid))
	{
		echo "Null arguments";
		return;		
	}

	$stmt = $conn->prepare("SELECT ID, SerialNumber, Activations, MaxActivations FROM SerialKeys WHERE SerialNumber=? AND ProductID=? AND Disabled=0");
	if (!$stmt)
	{
		//echo "Database request error";
		return;
	}

	$stmt->bind_param("ss", $key, $productid);
	$stmt->execute();

	$id = 0;
	$serialnumber = '';
	$activations = 0;
	$max_activations = 0;
		
	$stmt->bind_result($id, $serialnumber, $activations, $max_activations);		
	if (!$stmt->fetch())	// serial key not found
	{
		//echo "Key not found";
		return;
	}

	$stmt->close();

	// Number of activations exceeded.
	if ($activations >= $max_activations)
	{
		echo '1';  	// response code - number of activations exceeded
		return;	
	}

	// Increment activations count
	$conn->query("UPDATE SerialKeys SET Activations = Activations + 1 WHERE SerialNumber='" . $serialnumber . "'");	

	$left_activations = $max_activations - $activations - 1;
    if ($left_activations > 99)
		$left_activations = 99;
	
	//
	// Register user
	//	
	
	$stmt = $conn->prepare("SELECT ID FROM Customers WHERE Email=?");
	if (!$stmt)
		return;	
	$stmt->bind_param("s", $email);
	$stmt->execute();
	
	$userid = 0;
	$stmt->bind_result($userid);
	
	if (!$stmt->fetch())	// email not registered
	{
		$stmt2 = $conn->prepare("INSERT INTO Customers (FirstName, LastName, Email, SendEmails) VALUES(?,?,?,?)");
		if (!$stmt2)
			return;
			
		$stmt2->bind_param("ssss", $firstname, $lastname, $email, $sendemails);
		$stmt2->execute();
		$stmt2->close();
		
		$userid = $conn->insert_id;	
	}

	$stmt->close();

	// Update UserID for the used serial number.
	$conn->query("UPDATE SerialKeys SET CustomerID = " . $userid . " WHERE SerialNumber='" . $serialnumber . "'");	

	//
	// Sign the unique ID received from a client. It will be the activation code.
	//

	openssl_sign($uniqueid, $activation_signature, $private_key, OPENSSL_ALGO_SHA256);

	// Send OK
    echo '0'; // response code - ok
    if ($left_activations < 10)
		echo '0';
    echo $left_activations;
 	echo base64_encode($activation_signature);
}