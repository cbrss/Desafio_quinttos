<?php
    interface DatabaseInterface  {
        function query($sql, $params); 
        function connect();
        function disconnect();
    }
?>