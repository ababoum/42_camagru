# Vagrantfile
Vagrant.configure("2") do |config|
    # Use Ubuntu 20.04 as the base box
    config.vm.box = "ubuntu/focal64"
  
    # Forward port 8000 from the VM to the host machine
    config.vm.network "forwarded_port", guest: 8000, host: 8000
  
    # Provision the VM
    config.vm.provision "shell", inline: <<-SHELL
      # Update and install necessary packages
      sudo apt-get update
      sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common
  
      # Install Docker
      curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
      sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu focal stable"
      sudo apt-get update
      sudo apt-get install -y docker-ce
  
      # Install Docker Compose
      sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
      sudo chmod +x /usr/local/bin/docker-compose
  
      # Add vagrant user to the docker group
      sudo usermod -aG docker vagrant
  
      # Enable and start Docker service
      sudo systemctl enable docker
      sudo systemctl start docker
    SHELL
  
    # Sync the project directory to the VM
    config.vm.synced_folder ".", "/vagrant", type: "virtualbox"
  
    # Set VM resources
    config.vm.provider "virtualbox" do |vb|
      vb.memory = "1024"
      vb.cpus = 2
    end
  end