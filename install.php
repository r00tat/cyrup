<?php
/*
 * $RCSfile: install.php,v $ $Revision: 1.2 $
 * $Author: slim_lv $ $Date: 2007/01/05 13:17:23 $ 
 * This file is part of CYRUP project
 * by Yuri Pimenov (up@msh.lv) & Deniss Gaplevsky (slim@msh.lv)
 */

    require_once( "config.inc.php" );
    require_once( INCLUDE_DIR."/functions.inc.php" );
    require_once( INCLUDE_DIR."/html.inc.php" );

    require_once( INCLUDE_DIR."/".DB_TYPE.".inc.php" );

    function pc( $level ) {
        switch ( $level ) {
        case 1 :
            $head = "<font color='green'>OK</font>";
            break;
        case 2 :
            $head = "<font color='orange'>RISKY</font>";
            break;
        case 3 :
            $head = "<font color='orange'>BAD</font>";
            break;
        };
        return $head;
    };

    print_header( TITLE.": Install script");
?>
    <table with=600>
    <tr>
	<td>This is "first run, one time" script for <a href=cyrup.sf.net>Cyrup</a>.<br>
	   We assume that http server with php support is running, cyrus and database 
	    are configured as described in INSTALL file.<br>
	    Please be sure to set correct values of "SQL_PASS", "CYRUS_PASS", "PASSWORD_CRYPT" and "MAILBOX_STYLE"
	    in "includes/config.inc.php" file. <br>
	    This script checks for some php variables and set "cyrus" users password to database<br>
	    You may safely delete this file after successeful run
	</td>
    </tr>
    <tr>
	<td>Checking PHP variables:
	</td>
    </tr>
    <tr>
        <td>display_errors <?php print ( ini_get('display_errors') ? "ON: ".pc(2) : "OFF: ".pc(1) ); ?><br>
	    register_globals <?php print ( ini_get('register_globals') ? "ON: ".pc(2) : "OFF: ".pc(1) ); ?><br>
	    magic_quotes_gpc <?php print ( ini_get('magic_quotes_gpc') ? "ON: ".pc(3) : "OFF: ".pc(1) ); ?><br>
	    magic_quotes_runtime <?php print ( ini_get('magic_quotes_runtime') ? "ON: ".pc(3) : "OFF: ".pc(1) ); ?><br>
	    allow_url_fopen <?php print ( ini_get('allow_url_fopen') ? "ON: ".pc(2) : "OFF: ".pc(1) ); ?><br>
	    sql.safe_mode <?php print ( ini_get('sql.safe_mode') ? "ON: ".pc(3) : "OFF: ".pc(1) ); ?><br>
        </td>
    </tr>
    <tr>
        <td>Setting "cyrus" users password according to CYRUS_PASS variable:
<?php
    
    sql_query( "UPDATE cyrup_accounts SET password="
                .get_sql_crypt( CYRUS_PASS )." WHERE account='cyrus'");
    if ( sql_affected_rows() == 1 )
	print pc(1);
    else 
	print "Already set";

?> 
        </td>
    </tr>

    </table>    
<?php

    print_footer();

?>
