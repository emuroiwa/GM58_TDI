<?php
function foo() {
    return array(3, 'joe');
}

$data = foo();
$id = $data[0];
$username = $data[0];
echo $username;
// or:
list($id, $username) = foo();
?>