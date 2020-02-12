<?php
    #ini_set('display_errors', 1);
    #ini_set('display_startup_errors', 1);
    #error_reporting(E_ALL);

    session_start();
    
    $servername = "sql.njit.edu";
    $username = "dsp49";
    $password = ""; #:)
    $conn = mysql_connect($servername, $username, $password);
    mysql_select_db('dsp49', $conn);
    $action = $_REQUEST['action'];
    $info = $_REQUEST['info'];
    $user = $_SESSION['user_card'];
    $output = "";
    $query = "";
    $b = False;
    $bb = False; 
    switch($action){
        case "1":
            $query = "SELECT * FROM Patrons WHERE card_number = \"".$user."\";";
            #$query = "SELECT Patrons.first,Patrons.last,Patrons.card_number,Patrons.email,Books.book_id,Books.first AS author_first,Books.last AS author_last,Books.title,Books.call_number FROM Patrons,Books,CheckedOut WHERE Patrons.card_number = \"".$user."\" AND CheckedOut.card_number = \"".$user."\" AND Books.book_id = CheckedOut.book_id;";
            process($query);
            $query = "SELECT CheckedOut.card_number,Books.book_id,Books.title,Books.call_number,Books.first,Books.last FROM Books,CheckedOut WHERE Books.book_id = CheckedOut.book_id AND CheckedOut.card_number = \"".$user."\";"; 
            process($query);
            break;

        case "2":
            if(!mysql_fetch_assoc(mysql_query("SELECT * FROM Books WHERE title = \"".$info."\";"))){
                $b = True;
                break;
            }
            $query = "SELECT CheckedOut.card_number,Books.book_id,Books.title,Books.call_number,Books.first,Books.last FROM Books,CheckedOut WHERE Books.book_id = CheckedOut.book_id AND CheckedOut.card_number = \"".$user."\";"; 
            process($query); 
            mysql_query("INSERT INTO CheckedOut (card_number, book_id, due_date) SELECT \"".$user."\",book_id,\"12/27/2018\" FROM Books WHERE title=\"".$info."\";");
            process($query);
            #if($output == "Error"){
            #    break;
            #}
            break;
        case "3":
            if(!mysql_fetch_assoc(mysql_query("SELECT * FROM Books,CheckedOut WHERE Books.book_id = CheckedOut.book_id AND CheckedOut.card_number = \"".$user."\" AND Books.title = \"".$info."\";"))){
                $b = True;
                break;
            }
            $query = "SELECT CheckedOut.card_number,Books.book_id,Books.title,Books.call_number,Books.first,Books.last FROM Books,CheckedOut WHERE Books.book_id = CheckedOut.book_id AND CheckedOut.card_number = \"".$user."\";"; 
            process($query);
            mysql_query("DELETE FROM CheckedOut WHERE card_number = \"".$user."\" AND book_id IN (SELECT book_id FROM Books WHERE title = \"".$info."\");");
            process($query); 
            break;
        
        case "4":
            $arr = explode(',',$info);
            $arrtemp = explode(' ',$arr[0]);
            $first = $arrtemp[0];
            $last = $arrtemp[1];
            $cn = $arr[1];
            $email = "";
            if(count($arr) == 3){
                $email = $arr[2];
            } 
            $query = "INSERT INTO Patrons (first, last, card_number, email) VALUES (\"".$first."\",\"".$last."\",\"".$cn."\",\"".$email."\");";
            $rr = mysql_query($query);
            if(!$rr){
                $output = "FAILURE";   
            }else{
                $output = "SUCCESS";
                $_SESSION['user_card'] = $cn;
            }
            break;
    }

    function process($query){
        global $output, $b, $action,$bb;
        $result = mysql_query($query);
        if(!$result){
            #$output .= "RError";
        }
        else{   
            $output .= "<table><tbody><tr>";
            while($r = mysql_fetch_field($result)){
                $output .= "<th>".$r->name."</th>";
            }
            $output .= "</tr><tr>";
            while($row = mysql_fetch_assoc($result)){
                foreach($row as $j => $k){
                    $output .= "<td>".$k."</td>";
                }
                $output .= "</tr>";
            }
            $output .= "</tbody></table><br>";
            if(($action == "2" || $action == "3") && (!$bb)){
                $output .= "<h4>UPDATED</h4>";
                $bb = True;
            }else if(($action == "1") && (!$bb)){
                $output .= "<h4>BOOKS</h4>";
                $bb = True;
            }
            mysql_data_seek($result,0);
        }
    }

    mysql_close($conn);
    if($b){
        echo "Error";
    }else{
        echo $output;
    }
?>
