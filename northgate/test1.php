<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
$c="24/12/2013";
list($dd,$mm,$yyyy) = explode('/',$c);
if (!checkdate($mm,$dd,$yyyy)) {
       echo "jhkhasdjkl";
}
?>
</body>
</html>