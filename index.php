<?php

// --- EXPORTING DATA FROM A DATABASE TO A .CSV FILE ---

require_once('config.php');

$sql = new Sql();

$users = $sql->select("SELECT * FROM tb_usuario ORDER BY deslogin");

$headers = array();

foreach ($users[0] as $key => $value) {
    array_push($headers, ucfirst($key));
}

$file = fopen('users.csv', 'w+');

/**
 * The 'implode()' takes each array's element and turns it into a "text" where the elements will become words
 * separated by commas.
 */

fwrite($file, implode(",", $headers) . "\r\n");

foreach ($users as $row) { // 'foreach' for the registers (the "lines")
    $data = array();

    foreach ($row as $key => $value) { // 'foreach' for the register's columns
        array_push($data, $value);
    }

    fwrite($file, implode(",", $data) . "\r\n");
}

fclose($file);


// --- IMPORTING A .CSV FILE TO THE PHP ---

$filename = 'users.csv';

if (file_exists($filename)) {
    $file = fopen($filename, "r");

    /**
     * In this case, 'explode ()' literally explodes every time it finds a comma. At the end of everything we will
     * have an array, where each element that was separated by a comma becomes an element with index.
     */
    $headers = explode(",", fgets($file));

    $data = array(); // this varible will receive the final results

    while ($row = fgets($file)) {
        $rowData = explode(",", $row);

        /**
         * Thinking in an automated way (imagining that this code can be used in a lot of situations, where you
         * don't how many columns the file has) we have the code below. In a loop, the code count how my columns
         * the file has, adding to the array "line" (with the respective indices from "headers") the data from the
         * variable "rowData" has been receiving in the loop.
         */
        
        $line = array();

        for ($i = 0; $i < count($headers); $i++) {
            $line[$headers[$i]] = $rowData[$i];
        }

        array_push($data, $line);   
    }

    fclose($file);

    echo json_encode($data);
}

?>