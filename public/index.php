<?php
// Test: czy PHP i serwer działają
echo '<h1>HarcApp</h1>';
echo '<p>PHP działa! Wersja: ' . phpversion() . '</p>';
echo '<p>PDO PostgreSQL: ' . (extension_loaded('pdo_pgsql') ? 'tak' : 'nie') . '</p>';
