# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'yaml'

dir = Dir.pwd
vagrant_dir = File.expand_path(File.dirname(__FILE__))
vconfig = YAML::load_file( vagrant_dir + "/vagrant-config.yml" )

Vagrant.configure("2") do |config|

  config.vm.provider :virtualbox do |v|
    v.customize ["modifyvm", :id, "--memory", 512]
  end

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

	config.vm.provider :virtualbox do |vb, override|
			provision_target = 'virtualbox'
			override.vm.network :private_network, ip: vconfig['virtualbox']['private_ip']
			# config.cache.auto_detect = true
			# config.cache.enable_nfs  = true
			config.vm.synced_folder "www/", "/srv/www"
		end
		
	config.vm.provider :vmware_fusion do |vmware, override|
			provision_target = 'vmware_fusion'
			override.vm.box = 'debian70-x64-vmware'
			override.vm.network :private_network, ip: vconfig['vmware_fusion']['private_ip']
			override.vm.box_url = 'http://puppet-vagrant-boxes.puppetlabs.com/debian-70rc1-x64-vf503-nocm.box'
		  config.vm.synced_folder "www/", "/srv/www"
		end

	config.vm.provider :digital_ocean do |ocean, override|
			provision_target = 'digital_ocean'
			override.vm.box = "digital_ocean"
			override.vm.box_url = "https://github.com/smdahlen/vagrant-digitalocean/raw/master/box/digital_ocean.box"
			override.ssh.private_key_path = vconfig['digitalocean']['private_key']
			ocean.client_id = vconfig['digitalocean']['client_id']
			ocean.api_key = vconfig['digitalocean']['api_key']
			ocean.image = vconfig['digitalocean']['image']
			ocean.region = vconfig['digitalocean']['region']
	  end

  # System provision script
  if File.exists?(File.join(vagrant_dir,'provision','10-custom-system.sh')) then
		config.vm.provision :shell, :path => File.join( "provision", "10-custom-system.sh" ), :args => provision_target
	else
		config.vm.provision :shell, :path => File.join( "provision", "10-system.sh" ), :args => provision_target
  end
  # Server stack provision script
  if File.exists?(File.join(vagrant_dir,'provision','20-custom-serverstack.sh')) then
		config.vm.provision :shell, :path => File.join( "provision", "20-custom-serverstack.sh" ), :args => provision_target
	else
		config.vm.provision :shell, :path => File.join( "provision", "20-serverstack.sh" ), :args => provision_target
  end
  # DigitalOcean provision script
  if File.exists?(File.join(vagrant_dir,'provision','30-custom-setup.sh')) then
    config.vm.provision :shell, :path => File.join( "provision", "30-custom-setup.sh" ), :args => provision_target
	else
    config.vm.provision :shell, :path => File.join( "provision", "30-setup.sh" ), :args => provision_target
  end

end