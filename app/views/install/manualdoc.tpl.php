<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="center">
<tr>
<td>
<p>Manual Installaion (Try this if you're brave or semi-auto doesn't work)</p>
	 <ol>
	  <li>It's recommended you read <b>all these instructions before installing anything</b>; there's quite a bit to do</li>
	  <li>Install MySQL (other Databases might work, note: the weird junk about foreign keys)</li>
	  <li>Create a database for iPeer (typically called ipeer, peer, peer15, etc) (It's really easy to use the auto installer for this part)</li>
	  <li>Create the iPeer tables, here's a nice <a href="../sql/ipeer.sql">SQL script (for MySQL)</a></li>
	  <li>Configure Apache to run php scripts; see <a href="http://ca3.php.net/manual/en/faq.installation.php">http://ca3.php.net/manual/en/faq.installation.php</a></li>
	  <li><ul>
	  <li>Install PEAR (if it's not installed already) - you will need at minimum the DB package; see <a href="http://pear.php.net/manual/en/installation.php">http://pear.php.net/manual/en/installation.php</a></li>
	  <li>If you didn't install iPeer into your Apache web root directory, add an entry for Apache for ${your_ipeer_install_directory}</li>
	  </ul></li>
	  <li>Configure the ${your_ipeer_install_directory}"inc/config_params.inc.php" file</li>
	  <li><ul>
	  <li>define('UNIQUE_NAME', "StickWithAlphanumerics") - This is used to uniquely identiy the iPeer session (for session tracking and such); so use a unique name to avoid conflicts. See <a href="http://ca.php.net/session_name">http://ca.php.net/session_name</a></li>
	  <li>define('LOGIN_TEXT', "SomeHtml") - This text shows up in the top right corner(you can use html); useful for branding iPeer.</li>
	  <li>define('DEBUG',0); define('DEBUG_VERBOSITY', 1); - Leave these alone unless you want to see some nasty messages</li>
	  <li>define('DB_VENDOR', 'mysql'); - We recommend using MySQL (it's free and it's what we tested on). Since this is PEAR, for other databases see <a href="http://pear.php.net/package/DB/docs/latest/DB/DB.html#methodconnect">http://pear.php.net/package/DB/docs/latest/DB/DB.html#methodconnect</a></li>
	  <li>define('DB_USER', 'username'); The username to provide when connecting to the Database</li>
	  <li>define('DB_PASS', 'password'); The password (in plaintext no less!) to provide</li>
	  <li>define('DB_HOST', 'address'); The address (typically a dotted quad byte) for IP, although things like 'localhost' should work.</li>
	  <li>define('DB_NAME', 'database_name'); The name of the database you created for ipeer (which of course already contains the iPeer tables).</li>
	  <li>define('USE_EMAL_SCHEDULING', false); (Yes that's how it's spelt) a boolean for email scheduling. Uses *nix "at" (so if you're on windows, this had better be false).</li>
	  <li>define('ABS_URL', 'http://server/some_directories/ipeer_or_whatever_you_called_it'); The absolute directory path for iPeer. <b>Make sure there is no terminating '/'</b></li>
	  <li>define('DOMAIN', 'domain name'); The address for the iPeer webserver (typically a dotted quad byte), e.g. <a href="http://66.35.250.150">66.35.250.150</a></li>
	  <li>define('PHP_PATH', 'where php is'); Absolute path to php interpreter (use your O/S naming convention here).</li>
	  <li>define('ROOT_NAME', 'superman'); This is going to be the iPeer super user account name; refers to a user account in the system; basically the super admin can do anything</li>
	  <li>That's it. Don't change anything after these config lines</li>
	  </ul></li>
	  <li>Check out <a href="../">index.php</a> to see the ipeer homepage</li>
	  <li>Have a nice cup of alcoh^H^H^H^H^Hcoffee, 'cuz you're finally done installing the monster</li>
	 </ol>

</td>
</tr>
</table>

