# We include default installations of WordPress with this Vagrant setup.
# In order for that to respond properly, default databases should be
# available for use.
CREATE DATABASE IF NOT EXISTS `wp_oceandebrant`;
GRANT ALL PRIVILEGES ON `wp_oceandebrant`.* TO 'oceandebrant'@'localhost' IDENTIFIED BY 'oceandebrant';
