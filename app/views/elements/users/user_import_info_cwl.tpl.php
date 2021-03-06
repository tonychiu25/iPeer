<span class="required">NOTE</span>: <br />
<ul>
  <li>All fields mandatory, except email and password.</li>
  <li>If email column is missing, students will be requested to fill in when they log in the first time.</li>
  <li>If password column is missing, system will generate random password for each student.</li>
  <li>If an external authentication module is enabled (e.g. CWL or Shiboleth), password column can be ignored. Students will use external authentication module to login.</li>
  <li>Please make sure to remove header from the CSV file.</li>
</ul>
<br />
Formatting:
<pre style='background-color: white; border:1px solid black; padding:5px; margin:5px'>
Username,First Name,Last Name,Student#,<i>Email(optional),Password(optional)</i>
</pre>

<br />
Examples:<br />
<pre style='background-color: white; border:1px solid black; padding:5px; margin:5px'>
22928030, Sam,     Badhan,   22928030, sam@server.com, password123
78233046, Jamille, Borromeo, 78233046, jb@server.com,  pass5323123
39577051, Jordon,  Cheung,   39577051, jc@server.com,  psaswdrcD23
68000058, David,   Cliffe,   68000058, dc@server.com,  password123
</pre>
