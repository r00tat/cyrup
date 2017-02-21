<?php
/*
 * $RCSfile: config.inc.php,v $ $Revision: 1.12 $
 * $Author: slim_lv $ $Date: 2016/11/01 14:19:46 $
 * This file is part of CYRUP project
 * by Yuri Pimenov (up@fs.lv) & Deniss Gaplevsky (slim@inbox.lv)
 */

# Primary configuration options. This is where you should start.

    define( 'ADMIN_USER', 'admin' );

    define( 'BASE_URL', 'http'.( isset($_SERVER['HTTPS']) ? 's' : '' ).'://'
                                                .$_SERVER['HTTP_HOST']
                                                .dirname($_SERVER['SCRIPT_NAME']) );     
					// Where the root of this project is visible from web

    define( 'INCLUDE_DIR', 'includes' );
    define( 'AUTHSIGN', "<a href='http://cyrup.sf.net' target='_blank'>cyrup.sf.net</a>" );


    define( 'DB_TYPE', 'mysql' );   // Valid types are:
                                    //	"mysql" for MySQL (default)
                                    //	"pgsql" for PostgreSQL
    # SQL
    define( 'SQL_HOST', 'localhost' );
    define( 'SQL_DB',   'mail' );
    define( 'SQL_USER', 'postfix' );
    define( 'SQL_PASS', 'mA1L' );

    # Cyrus
    define( 'CYRUS_HOST', 'localhost' );
    define( 'CYRUS_PORT', 143 );
    define( 'CYRUS_USER', 'cyrus' );
    define( 'CYRUS_PASS', 'itest' );

    define( 'CYRUS_DELIMITER', '/' );   // Default delimiter is '.'
                                        // if unixhierarchysep is set to 1 in imapd.conf
                                        // then delimiter must be '/'

    define( 'MAILBOX_STYLE', 'USER@DOMAIN.TLD' ); // Available: USERSUFFIX, USER@DOMAIN.TLD
                                                  // "USERSUFFIX"
                                                  //    Mailboxes will be created with the suffix 
                                                  //    specified for each domain.
                                                  //    For examaple "user_bogus", where suffix 
                                                  //    is "_bogus".
                                                  //    Suffix may be empty only for one domain.
                                                  // "USER@DOMAIN.TLD" (RECOMMENDED)
                                                  //    This style mailboxes works as 
                                                  //    virtual_mailbox_maps

    define( 'DEFAULT_DOMAIN', '' );       // when MAILBOX_STYLE is set to USER@DOMAIN.TLD it is possible to
                                          // set DEFAULT_DOMAIN same as defaultdomain in imapd.conf.
                                          // This works with saslauthd only

    define( 'PASSWORD_CRYPT', 4 );	// The same meaning as for pam_mysql crypt parameter
                                    // 0 - means clear text passwords,
                                    // 1 - UNIX crypt()
                                    // 2 - use MySQL's PASSWORD() function 
                                    //     NOT VALID FOR PGSQL
                                    //     (NOT RECOMMENDED)
                                    // 3 - MD5
                                    // 4 - SHA1 (RECOMMENDED)
                                    // See pam_mysql docs for details

# User interface & miscelaneous options

    define( 'SHOW_PASSWORD', false );   // true - one html "<input type=text ..." field 
                                        //  (usefull only with PASSWORD_CRYPT=0)
                                        // false - two html "<input type=password ..." 
                                        //  input fields (enter & reenter)

    define( 'MIN_PASSWORD_LENGTH', 4 ); // Less is not allowed
    define( 'DEFAULT_QUOTA', 50 );      // Default user quota in megabytes
    define( 'ALLOW_NO_QUOTA', 1 );      // While "true" admin can disable user's quota 
                                        //	by setting it to "0"
    define( 'SHOW_VACATION_LIST', 1 );  // Report vacation status in list of accounts (may be slow)

    define( 'DOMAIN_EXPORT_FILE', '/etc/postfix/local.domains' ); // Path to file where to save 
                                        // the list of domains (one per line).
                                        // May be used to speed-up domain search.
                                        // A webserver's user needs to have write permissions to this file.
                                        // Leave empty when not used.

    define( 'SYSTEM_ALIASES', '/etc/postfix/aliases.pcre' ); 
                                        // Export system domain names for system aliases ( postmaster etc )
                                        // Create file with correct permissions or leave blank to skip

    define ( 'SESS_LIFE' , get_cfg_var('session.gc_maxlifetime'));

    define ('IMAP_FOLDERS', 'SPAM, Sent ' );  // List of IMAP folders, separated by comma to be created for new accounts (but not subscribed)

    define( 'JS_URL', BASE_URL.'/js' );         // Where all JavaScript includes lie
    define( 'IMAGES_URL', BASE_URL.'/img' );    // Images

    define( 'VERSION', ' CyrUp v2.4.1' );         // Version. The bigger the better
    define( 'TITLE', VERSION.': ' );            // Prepend this to html title

    # Debug level & verbose messages
    define( 'D_NONE', 0 );           // no debug messages
    define( 'D_INCLUDE', 1 );        // include() or require()
    define( 'D_FUNCTION', 2 );       // most function calls
    define( 'D_SQL_ERROR', 4 );      // sql errors
    define( 'D_IMAP_ERROR', 8 );     // imap errors
    define( 'D_ALL', 255 );          // full verbose

    define ( 'DEBUG_LEVEL', D_SQL_ERROR | D_IMAP_ERROR );
#    define ( 'DEBUG_LEVEL', D_ALL );
#    define ( 'DEBUG_LEVEL', D_SQL_ERROR | D_FUNCTION | D_INCLUDE );

?>
