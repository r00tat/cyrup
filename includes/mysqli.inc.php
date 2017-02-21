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
        print mysqli_error();
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
        DEBUG( D_FUNCTION, "mysqli_escape()" );
        return @mysqli_real_escape_string($GLOBALS['mysqlidb'], $str) or sql_die("failed to escape string");
    }

    sql_connect();

?>