# -*- mode: ruby -*-
# vi: set ft=ruby :

dir = Dir.pwd
vagrant_dir = File.expand_path(File.dirname(__FILE__))

Vagrant.configure("2") do |config|

  # Store the current version of Vagrant for use in conditionals when dealing
  # with possible backward compatible issues.
  vagrant_version = Vagrant::VERSION.sub(/^v/, '')

  # Forward Agent
  #
  # Enable agent forwarding on vagrant ssh commands. This allows you to use identities
  # established on the host machine inside the guest. See the manual for ssh-add
  config.ssh.forward_agent = true
	config.vm.box = "wheezy32"

	config.vm.provider :virtualbox do |vb|
		vb.vm.network :private_network, ip: "192.168.9.99"
		end
		
	config.vm.provider :digital_ocean do |ocean|
	  	ocean.vm.box = "digital_ocean"
			ocean.vm.hostname = "travels-wp"
			ocean.client_id = "6cf13f5b24e2a264646024935b9c5239"
			ocean.api_key = "8647e47d6d4d7e9aaf503269e950d6ee"
			ocean.image = "Debian 7.0 x64"
			ocean.region = "Amsterdam 2"
			ocean.ssh.private_key_path = "/Users/pixline/.ssh/id_rsa"
	  end
		
  # /srv/config/
  config.vm.synced_folder "config/", "/srv/config"
  config.vm.synced_folder "www/", "/srv/www"

  # /srv/www/
  #if vagrant_version >= "1.3.0"
  #  config.vm.synced_folder "www/", "/srv/www/", :owner => "www-data", :mount_options => [ "dmode=775", "fmode=774" ]
  #else
  #  config.vm.synced_folder "www/", "/srv/www/", :owner => "www-data", :extra => [ "dmode=775", "fmode=774" ]
  #end

  # System provision script
  if File.exists?(File.join(vagrant_dir,'provision','10-system.sh')) then
    config.vm.provision :shell, :path => File.join( "provision", "10-system.sh" )
  end

  # Custom provision script
  if File.exists?(File.join(vagrant_dir,'provision','20-custom.sh')) then
    config.vm.provision :shell, :path => File.join( "provision", "20-custom.sh" )
  end

  # Websites provision script
  if File.exists?(File.join(vagrant_dir,'provision','30-websites.sh')) then
    config.vm.provision :shell, :path => File.join( "provision", "30-websites.sh" )
  end

end

