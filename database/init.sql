# We include default installations of WordPress with this Vagrant setup.
# In order for that to respond properly, default databases should be
# available for use.
CREATE DATABASE IF NOT EXISTS `wordpress_dev`;
GRANT ALL PRIVILEGES ON `wordpress_dev`.* TO 'wp'@'localhost' IDENTIFIED BY 'password';
