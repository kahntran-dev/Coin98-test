locals {
  vpc_id           = "vpc-02d35bc77992203bc"
  subnet_id        = "subnet-08a7af30068c6097a"
  ssh_user         = "ubuntu"
  key_name         = "devops"
  private_key_path = "_aws/devops.pem"
}

variable "git_user" {
  description = "GitHub Username"
  type        = string
  sensitive   = true
}

variable "git_password" {
  description = "GitHub Password"
  type        = string
  sensitive   = true
}

provider "aws" {
  access_key = "..."
  secret_key = "..."
  region = "us-east-1"
}

resource "aws_security_group" "webapp" {
  name   = "webapp_access"
  vpc_id = local.vpc_id

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }
}

resource "aws_instance" "webapp" {
  ami                         = "ami-0dba2cb6798deb6d8"
  subnet_id                   = local.subnet_id
  instance_type               = "t2.micro"
  associate_public_ip_address = true
  security_groups             = [aws_security_group.webapp.id]
  key_name                    = local.key_name

  provisioner "remote-exec" {
    inline = ["echo 'Wait until SSH is ready'"]

    connection {
      type        = "ssh"
      user        = local.ssh_user
      private_key = file(local.private_key_path)
      host        = aws_instance.webapp.public_ip
    }
  }
  provisioner "local-exec" {
    command = "ansible-playbook  -i ${aws_instance.webapp.public_ip}, --private-key ${local.private_key_path} -e \"git_user='${var.git_user}' git_password='${var.git_password}'\" webapp.yaml"
  }
}

output "webapp_ip" {
  value = aws_instance.webapp.public_ip
}
