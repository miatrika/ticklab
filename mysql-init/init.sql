-- Autorise root à se connecter depuis n'importe quelle IP 
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY '15182114';
FLUSH PRIVILEGES;

