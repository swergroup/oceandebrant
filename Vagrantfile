# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'yaml'

dir = Dir.pwd
vagrant_dir = File.expand_path(File.dirname(__FILE__))
vconfig = YAML::load_file( vagrant_dir + "/vagrant-config.yml" )

Vagrant.configure("2") do |config|

  # Store the current version of Vagrant for use in conditionals when dealing
  # with possible backward compatible issues.
  vagrant_version = Vagrant::VERSION.sub(/^v/, '')
	provision_target = ''
	
	# Forward Agent
  #
  # Enable agent forwarding on vagrant ssh commands. This allows you to use identities
  # established on the host machine inside the guest. See the manual for ssh-add
  config.ssh.forward_agent = true
	config.vm.box = "wheezy32"
	config.vm.box_url = "http://tools.swergroup.com/downloads/wheezy32.box"
	config.vm.hostname = vconfig['hostname']

  # /srv/config/
  # config.vm.synced_folder "config/", "/srv/config"
  config.vm.synced_folder "www/", "/srv/www"

	config.vm.provider :virtualbox do |vb, override|
		override.vm.network :private_network, ip: vconfig['virtualbox']['private_ip']
		provision_target = 'virtualbox'
		end
		
	config.vm.provider :digital_ocean do |ocean, override|
	  	override.vm.box = "digital_ocean"
			override.vm.box_url = "https://github.com/smdahlen/vagrant-digitalocean/raw/master/box/digital_ocean.box"
			ocean.client_id = vconfig['digital_ocean']['client_id']
			ocean.api_key = vconfig['digital_ocean']['api_key']
			ocean.image = vconfig['digital_ocean']['image']
			ocean.region = vconfig['digital_ocean']['region']
			override.ssh.private_key_path = vconfig['digital_ocean']['private_key']
			provision_target = 'digital_ocean'
	  end

  # System provision script
  if File.exists?(File.join(vagrant_dir,'provision','10-system.sh')) then
		config.vm.provision :shell, :path => File.join( "provision", "10-system.sh" )
  end
  # Server stack provision script
  if File.exists?(File.join(vagrant_dir,'provision','20-serverstack.sh')) then
		config.vm.provision :shell, :path => File.join( "provision", "20-serverstack.sh" ), :args => provision_target
  end
  # DigitalOcean provision script
  if File.exists?(File.join(vagrant_dir,'provision','20-virtualbox.sh')) then
    config.vm.provision :shell, :path => File.join( "provision", "20-virtualbox.sh" )
  end

end