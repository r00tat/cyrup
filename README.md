# Introduction

CyrUp is web interface for mail system based on Postfix, Cyrus and MySQL/PostgreSQL software.

This project is forked from https://sourceforge.net/projects/cyrup/

# Prerequisites

To use this software, you need the following stuff:
 - A *NIX (or similar) system, with PAM facility (when non-plain text passwords are used) is set up and working
 - A MySQL or PostgreSQL server, up and running
 - Postfix MTA 
 - Cyrus imap
 - Cyrus SASL (when non-plain text passwords are used)
 - Http server with PHP4/5 support 
 - Net_Sieve PEAR class for autoreply support

# Installation instruction

See INSTALL file for detail.

# Notes (known issues)

1. Domain suffix (when MAILBOX_STYLE is "USERSUFFIX") can be set at new domain creation time only (in Domains tab) - there is no posibility to change domain suffix after domain creation.
2. When alias is renamed (in Aliases tab) and this alias is in any maillist (in Maillists tab) - alias in maillist will not be renamed automaticly.
3. When alias is aliased to email (in Aliases tab), destinations email need to be separeted with ','.
4. When adding new account (in Accounts tab) and 'Autocreate alias?' is checked, CYRUP checks existence of corresponding alias, but silently drop alias creation if alias already exist.
