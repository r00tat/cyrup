<?php
/*
 * $RCSfile: html.inc.php,v $ $Revision: 1.9 $
 * $Author: slim_lv $ $Date: 2016/11/01 14:09:36 $
 * This file is from CYRUP project
 * by Yuri Pimenov (up@msh.lv) & Deniss Gaplevsky (slim@msh.lv)
 */

  if ( !defined("INCLUDE_DIR") ) exit("Not for direct run");

  DEBUG( D_INCLUDE, "html.inc.php" );


  function print_header( $title = VERSION ) {
	
    $GLOBALS['execution_start'] = explode( " ", microtime() );	
    print '<html><head><title>'.$title.'</title>'."\n";
    print '<link href="'.BASE_URL.'/def.css" rel="stylesheet" type="text/css">'."\n";
    print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
    print '</head><body bgcolor=white>'."\n";
  }

  function print_footer() {
	
    session_write_close();

    $execution_end = explode( " ", microtime() );
    $execution_time = ( $execution_end[1] - $GLOBALS['execution_start'][1] );
    $execution_time += ( $execution_end[0] - $GLOBALS['execution_start'][0] );

    print '<br><center><font face=Courier size=2>Page generation time: ';
    print number_format( $execution_time, 3, '.', '' ).'s<br>';
    print VERSION.'</font><br>'.AUTHSIGN."</center>\n";
    print "</body></html>\n";
  }

  function print_top_menu() {

    print "<table>\n<tr>\n<td align=left width=1%><img src='".IMAGES_URL."/logo.gif'></td>\n";
    print "<td bgcolor='#FFFFFF'>\n<center>";

    $scripts = array(
        'Domains'    => array( "domains", "domainform" ),
        'Accounts'   => array( "accounts", "accountform" ),
        'Aliases'    => array( "aliases", "aliasform" ),
        'Maillists'  => array( "maillists", "maillistform" ),
        'Service'    => array( "service" )
    );

    if ( $_SESSION['USER'] == ADMIN_USER ) 
        $scripts['Admins'] = array( "admins", "adminform" );

    $scripts['Logout'] = array( "logout" );

    while ( list( $cur_item, $scripts_list ) = each( $scripts ) ) {	
        $css_class = "button";
        if ( in_array( $_GET['m'], $scripts_list ) ) $css_class .= "_sel";
        print "&nbsp;<a class='".$css_class."' href='?admin&m=".$scripts_list[0]."'>".$cur_item."</a>&nbsp;\n";
    }

    print "</center>\n</td></tr></table>\n";
  }

  function dotline( $colspan )  {

    print '<tr><td colspan='.$colspan.' background="'.IMAGES_URL.'/dotline.gif" height=1>';
    print '<img src="'.IMAGES_URL.'/x.gif" width=1 height=1></td></tr>'."\n";
  }

  /* html_*() functions below are to produce shorter php code. */
    
  // Prints table header "<th>" with link to calling script with $order_by in request.
  // $order_by used for sorting. Used in accounts.php, aliases.php etc.

  function html_th( $field_name, $th_header, $title = '' ) {
    global $order_by;

    print '<th '.( $order_by == $field_name ? ' class=selected' : '' );
    print '><a href="?admin&m='.$_GET['m'].'&order_by='.$field_name.'" title="'.$title.'">&nbsp;';
    print $th_header."&nbsp;</a></th>\n";
  }

  // This function prints table's row with 2 columns in it.
  // $desc goes to the first column, all others variables used for text input field in the second column.
  // Used mostly in *form.php files (accounform.php, aliasform.php etc).

  function html_input_text( $field_name, $desc, $value = '', $desc_suffix = '', $size = 15 ) {

    print '<tr><td>&nbsp; '.$desc.' &nbsp;</td>'."\n";
    print '<td><input type="text" name="'.$field_name.'" value="'.$value.'"';
    print ( $size ? ' size='.$size : '' ).'>'.( $desc_suffix != '' ? $desc_suffix.' &nbsp;' : '' ).'</td>';
    print "</tr>\n";
  }

  function delete_selected_box() {

    print '<br /><table align="center" border=0 cellpadding=0 cellspacing=0>';
    dotline( 3 ); 
    print '<tr><td> &nbsp; Delete selected ? &nbsp;';
    print '<input type=checkbox name=confirm value="yes"> Yes&nbsp;</td>';
    print '<td>&nbsp;<input type=submit value="Delete">&nbsp;</td></tr>'."\n";
    dotline( 3 ); 
    print "</table>\n";
  }

  function print_errors( $errors ) {

    print '<font face=courier color=red><b>';
    reset( $errors );
    while ( $val = current( $errors ) ) {
        print $val."<br />\n";
        next( $errors );
    }
    print "</b></font>\n";
  }

  function print_domain_selection( $domain_id ) {

    $domain_id = intval($domain_id);
    $query = 'SELECT * FROM cyrup_domains '.rights2sql(1).' ORDER BY domain';
    sql_query( $query );
    print '<form action="".BASE_URL."/?admin&m='.$_GET['m'].'" method="POST">'."\n";
    print '<select name="domain_id" OnChange="submit();">'."\n";
    print '<option value=0>--- Select domain here ---</option>'."\n";

    $domain_row = FALSE;
    while ( $row = sql_fetch_array() ) {
        print '<option value="'.intval($row['id']).'"';
        if ( $row['id'] == $domain_id ) {
            print ' selected';
            $domain_row = TRUE;
        }
        print '>'.$row['domain']."</option>\n";
    }
    print '</select><input type=submit value="Go">';

    if ( ($domain_id) AND ($domain_row) ) {
        $domain_row = get_domain_info($domain_id);
        print '&nbsp;&nbsp;';
        print '<b>Quota:</b> '.kb2mb( $domain_row['quota_cur'] ).'/'.kb2mb( $domain_row['quota'] );
        print "&nbsp;\n";
        print '<b>Accounts:</b> '.$domain_row['accounts_cur'].'/'.$domain_row['accounts_max'];
        print "&nbsp;\n";
        print "<b>Aliases:</b> ".$domain_row['aliases_cur'].'/'.$domain_row['aliases_max'];
        print "&nbsp;&nbsp;\n";
        print_filter();
    }
    print '</form>';
  }

  function print_filter() {

    $current = ( empty($_GET['w']) ? '' : $_GET['w']  );
    $qs = preg_replace('/&w=.*((?=&)|$)/','',$_SERVER['QUERY_STRING']);
    print ( empty($current) ? 'Filter' : '<strong>Filter</strong>' ).':&nbsp;|&nbsp;';

    for ( $i = 97; $i <= 122 ; $i++ ) {
        print '<a href="?'.$qs.'&w='.chr($i).'">';
        print ( $current == chr($i) ? chr($i-32) : chr($i) ).'</a>&nbsp;&nbsp;';
    }
    print '<a href="?'.$qs.'&w=0">0</a>&nbsp;|&nbsp;';
    print '<a href="?'.$qs.'">'.( empty($current) ? 'ALL' : 'all' ).'</a>'."\n";
  }

  function kb2mb( $value ) {

    return number_format( $value / 1024, 2, '.', '' );
  }

  function percents( $part, $full = 0 ) {

    $rval = ( empty($full) ? $rval = 'n/a' : intval( $part / $full * 100 ).'%' );
    return ( ($full AND ($part >= $full)) ? '<font color=red>'.$rval.'</font>' : $rval);
  }

?>
