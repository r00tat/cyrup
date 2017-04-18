<?php
/*
 * $RCSfile: mysql.inc.php,v $ $Revision: 1.5 $
 * $Author: slim_lv $ $Date: 2016/11/01 14:09:36, Paul Woelfel <github@frig.at> $
 * This file is part of CYRUP project
 * by Yuri Pimenov (up@msh.lv) & Deniss Gaplevsky (slim@msh.lv)
 */

    if ( !defined("INCLUDE_DIR") ) exit("Not for direct run");

    DEBUG( D_INCLUDE, "sql.inc.php" );
    $GLOBALS['mysqlidb'] = null;
    $GLOBALS['sql_last_result'] = null;

    function sql_die( $message ) {
        print "<font color=red><b>FATAL: </b></font>";
        DEBUG( D_SQL_ERROR, $message );
        $err_message = $GLOBALS['mysqlidb'] === null || $GLOBALS['mysqlidb'] === FALSE ? mysqli_connect_error() : mysqli_error($GLOBALS['mysqlidb']);
        DEBUG( D_SQL_ERROR, "SQLERR: ".$err_message);
        print $err_message;
        exit();
    }

    function sql_connect( $database=SQL_DB, $username=SQL_USER, $password=SQL_PASS ) {
        DEBUG( D_FUNCTION, "mysqli_connect('$database', '$username', '\$password')" );

        ($GLOBALS['mysqlidb'] = @mysqli_connect( SQL_HOST, $username, $password,  $database))
            or sql_die( "mysqli_connect(): Couldn't connect to the database" );
    }

    function sql_select_db( $database = SQL_DB ) {
        DEBUG( D_FUNCTION, "mysqli_select_db('$database')" );

        return @mysqli_select_db( $GLOBALS['mysqlidb'], $database )
            or sql_die( "mysqli_select_db(): Couldn't select the database" );
    }

    function sql_data_seek() {

        DEBUG( D_FUNCTION, "mysqli_data_seek(...)" );

        if ( func_num_args() == 1 ) {
            $result = $GLOBALS['sql_last_result'];
            $row_number = func_get_arg( 0 );
        } else {
            $result = func_get_arg( 0 );
            $row_number = func_get_arg( 1 );
        }

        return mysqli_data_seek( $result, $row_number );
    }

    function sql_query( $query ) {

        if(empty($GLOBALS['mysqlidb'])){
            sql_connect();
        }

        DEBUG( D_FUNCTION, "mysqli_query('$query')" );

        ($GLOBALS['sql_last_result'] = @mysqli_query($GLOBALS['mysqlidb'], $query ))
            or sql_die( "mysqli_query(): Unable to execute query: $query" );

        return  $GLOBALS['sql_last_result'];
    }

    function sql_num_rows() {

        DEBUG( D_FUNCTION, "mysqli_num_rows(...)" );

        if ( ! func_num_args() )
            $result = $GLOBALS['sql_last_result'];
        else
            $result = func_get_arg( 0 );

        return mysqli_num_rows($result );
    }

    function sql_affected_rows() {

        DEBUG( D_FUNCTION, "mysqli_affected_rows()" );

        return mysqli_affected_rows($GLOBALS['mysqlidb']);
    }

    function sql_insert_id() {

        DEBUG( D_FUNCTION, "mysqli_insert_id()" );

        return mysqli_insert_id($GLOBALS['mysqlidb']);
    }

    function sql_fetch_array() {

        DEBUG( D_FUNCTION, "mysqli_fetch_array(...)" );

        if ( ! func_num_args() )
            $result = $GLOBALS['sql_last_result'];
        else
            $result = func_get_arg( 0 );

        return mysqli_fetch_array($result );
    }

    function sql_fetch_variable() {

        DEBUG( D_FUNCTION, "mysqli_fetch_variable(...)" );

	if ( ! func_num_args() )
	    $result = $GLOBALS['sql_last_result'];
        else
            $result = func_get_arg( 0 );

        $arr = sql_fetch_array( $result );
        if ( $arr == false )
            return false;

        return $arr[0];
    }

    function sql_fetch_row() {

        DEBUG( D_FUNCTION, "mysqli_fetch_row(...)" );

        if ( ! func_num_args() )
            $result = $GLOBALS['sql_last_result'];
        else
            $result = func_get_arg( 0 );

        return mysqli_fetch_row( $result );
    }

     // Can be used for caching. Usage: sql_export( $query, $filename );
    function sql_export( $query, $filename ) {

        $S_NEWLINE = "\n";   // New line
        $S_COMMENT = "#";    // Comment sign
        $S_DELIMITER = "\t"; // Fields delimiter


        DEBUG( D_FUNCTION, "mysqli_export('$query','$filename')" );
     
        if ( ( $fh = fopen( $filename, "w" ) ) === FALSE ) sql_die( "mysqli_export(): Permission denied" );
 
        $result = sql_query( $query );
        $f_count = mysqli_num_fields( $result );
        fwrite( $fh, $S_COMMENT." " ); // Comment sign
        while ( $field = mysqli_fetch_field($result) ) fwrite( $fh, $field->name.$S_DELIMITER ); 
        fwrite( $fh, $S_NEWLINE );
 
        while ( $row = sql_fetch_array( $result ) ) {
            for ( $i = 0; $i < $f_count; $i++ ) {
                fwrite( $fh, str_replace(
                    array( "\\",   $S_COMMENT,      $S_NEWLINE, $S_DELIMITER ),
                    array( "\\\\", "\\".$S_COMMENT, "\\n",      "\\t" ),
                    $row[$i] )
                    .$S_DELIMITER );
            }
            fwrite( $fh, $S_NEWLINE );
        }
 
        fclose( $fh );
        return mysqli_fetch_array( $result );
    }

    function sql_free_result() {

        DEBUG( D_FUNCTION, "mysqli_free_result(...)" );

        if ( ! func_num_args() )
            $result = $GLOBALS['sql_last_result'];
        else
            $result = func_get_arg( 0 );

        return @mysqli_free_result( $result )
            or sql_die( "mysqli_free_result(): Couldn't free result" );
    }

    function sql_close() {

        DEBUG( D_FUNCTION, "mysqli_close()" );

        mysqli_close();
    }

    function sql_escape($str){
        DEBUG( D_FUNCTION, "mysqli_escape($str)" );
        if (empty($GLOBALS['mysqlidb'])){
            sql_connect();
        }

        if(strlen(strval($str)) == 0 ){
            return "";
        }
        $escape_string = @mysqli_real_escape_string($GLOBALS['mysqlidb'], strval($str)) or sql_die("failed to escape string: ".$str." ".json_encode(error_get_last()));
        // DEBUG( D_FUNCTION, "mysqli_escape($str) = $escape_string" );
        return $escape_string;
    }

    function sql_check_tables(){
        // fetch tables
        $result = sql_query("show tables;");

        $tables = [];
        while ($row = mysqli_fetch_row($result)){
            $tables[] = $row[0];
        }

        mysqli_free_result($result);

        if(!in_array("cyrup_accounts", $tables)){
            DEBUG(D_FUNCTION, "creating tables!");
            $sql = file_get_contents('scripts/cyrup.mysql.sql');
            $sql = preg_replace('#/\*.*?\*/#s', '', $sql);
            $sql = preg_replace('/^--.*[\r\n]*/m', '', $sql);
            // $sql = preg_replace('/\n(?!;)/m', ' ', $sql);
            DEBUG(D_FUNCTION, "executing query: ".$sql);
            if (mysqli_multi_query($GLOBALS['mysqlidb'], $sql)){
                do {
                    $result = mysqli_store_result($GLOBALS['mysqlidb']);
                    if ($result === TRUE){
                        DEBUG(D_FUNCTION, "table created.");
                    }elseif ($result) {
                        while ($row = mysqli_fetch_row($result)) {
                             DEBUG(D_FUNCTION, "row: ". json_encode($row));
                        }
                        mysqli_free_result($result);
                    }
                } while (mysqli_next_result($GLOBALS['mysqlidb']));

            }else {
                sql_die("create tables failed!");
            }
        }

        // -- INSERT INTO cyrup_admins VALUES (DEFAULT,'admin',SHA1('admin'),'','Mega admin');
        // -- INSERT INTO cyrup_accounts VALUES (DEFAULT,'cyrus',SHA1('cyrus'),0,0,DEFAULT,DEFAULT,DEFAULT,DEFAULT,'cyrus admin','1');
        $result = sql_query("SELECT COUNT(id) FROM cyrup_admins");
        $row = mysqli_fetch_row($result);
        mysqli_free_result($result);
        if ($row[0] < 1){
            // no admins exist
            if (sql_query("INSERT INTO cyrup_admins VALUES (DEFAULT,'admin',".get_sql_crypt('admin').",'','Super admin');")){
                DEBUG(D_FUNCTION, "created admin user");
            }else {
                sql_error("failed to create admin user");
            }
        }

        $result = sql_query("SELECT COUNT(id) FROM cyrup_accounts WHERE account = 'cyrus'");
        $row = mysqli_fetch_row($result);
        mysqli_free_result($result);
        if ($row[0] < 1){
            // cyrus does not exist
            if (sql_query("INSERT INTO cyrup_accounts VALUES (DEFAULT,'cyrus',".get_sql_crypt(CYRUS_PASS).",0,0,DEFAULT,DEFAULT,DEFAULT,DEFAULT,'cyrus admin','1');")){
                DEBUG(D_FUNCTION, "created cyrus user");
            }else {
                sql_error("failed to create cyrus user");
            }
        }

    }

    sql_connect();
    sql_check_tables();

?>