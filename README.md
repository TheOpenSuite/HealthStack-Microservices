# 🏥 Hospital Management Information System (HMIS)

[![License](https://img.shields.io/badge/License-Apache%202.0-red.svg)](https://opensource.org/licenses/Apache-2.0)
![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/theopensuite/HealthStack-Microservices)


Welcome to the **Hospital Management Information System (HMIS)**, an open-source project showcasing a modern, scalable, and resilient **microservices architecture** for healthcare operations. This project refactors a traditional monolithic application into independent, role-based services, each with its own dedicated interface.

## Table of Contents
- [✨ Key Features & Architecture](#-key-features--architecture)
  - [Core HMIS Modules](#core-hmis-modules)
  - [New Enhancements](#new-enhancements)
- [🛠️ DevOps & DevSecOps: A Modern CI/CD Pipeline](#️-devops--devsecops-a-modern-cicd-pipeline)
  - [1. Containerization with Docker 🐳](#1-containerization-with-docker-)
  - [2. CI/CD with GitHub Actions ⚙️](#2-cicd-with-github-actions-️)
  - [3. Infrastructure as Code (IaC) with Terraform ☁️](#3-infrastructure-as-code-iac-with-terraform-️)
  - [4. Configuration Management with Ansible 🤖](#4-configuration-management-with-ansible-)
  - [5. Orchestration with Kubernetes ⛵](#5-orchestration-with-kubernetes-️)
- [🚀 Getting Started](#-getting-started)
  - [Prerequisites](#prerequisites)
  - [Step 1: Set Up the Local Virtual Machine](#step-1-set-up-the-local-virtual-machine)
  - [Step 2: Access the Services](#step-2-access-the-services)
    - [Option A: Port Forwarding (Recommended)](#option-a-port-forwarding-recommended)
    - [Option B: Reverse SSH Tunnel](#option-b-reverse-ssh-tunnel)
  - [Step 3: Deploy to the Kubernetes Cluster](#step-3-deploy-to-the-kubernetes-cluster)
- [🤝 Contribution](#-contribution)

---

## ✨ Key Features & Architecture

### **Core HMIS Modules**
Our system is built around key modules to streamline hospital workflows.

* **Patient Management**: Comprehensive patient registration, medical records, and integrated workflows.
* **Department-Specific Systems**: Dedicated dashboards and tools for key departments like **Pharmacy**, **Reception**, **Administration**, and **Doctors**.
* **Financial Management**: Robust systems for billing, payroll, and vendor management.
* **Security**: Enhanced security via **Role-Based Access Control (RBAC)**, ensuring users only have access to what's necessary.

### **New Enhancements**
We've made significant improvements to core functionality.

* **Improved Pharmacy Management**: A specialized module for pharmacy staff with drug inventory tracking, supplier management, and automated expiry date alerts. 
* **Advanced Reception Dashboard**: A streamlined system for patient check-ins, appointment scheduling, and doctor selection based on real-time availability.

---

## 🛠️ DevOps & DevSecOps: A Modern CI/CD Pipeline

This project is a practical demonstration of a full **DevOps pipeline**, leveraging open-source tools to automate the software development lifecycle from code commit to deployment.

### 1. **Containerization with Docker 🐳**
Every microservice is packaged into its own **Docker container**. This ensures consistency and portability across development, testing, and production environments. Each service's directory includes a `Dockerfile` that defines its build process.

### 2. **CI/CD with GitHub Actions ⚙️**
Our `ci-cd.yml` workflow automates the entire process.

* **Static Analysis**: The pipeline starts with a `pre_build_security_scan` using **TruffleHog** to scan for secrets and sensitive data before the build even begins.
* **Build & Scan**: The `build_and_scan_images` job builds each service's Docker image, pushes it to Docker Hub, and then uses **Trivy** to scan for vulnerabilities. The build automatically fails if any high-severity issues are detected.
* **Automated Deployment**: A `deploy` job is configured to automatically trigger a deployment to our Kubernetes cluster using **Ansible** upon a successful build and scan.

### 3. **Infrastructure as Code (IaC) with Terraform ☁️**
The `terraform/` directory contains scripts to provision a local virtual machine using the **Libvirt** provider.

* `main.tf`: Defines the virtual machine's resources (CPU, memory, networking) and uses a **cloud-init** script to configure the hostname and inject an SSH key for secure access.
* `output.tf`: Provides the IP address of the newly provisioned VM.

### 4. **Configuration Management with Ansible 🤖**
Our `ansible/playbook.yml` automates the setup and deployment to the VM.

* **Installation**: It checks for and installs **k3s**, a lightweight Kubernetes distribution.
* **Deployment**: It applies all Kubernetes manifest files to the cluster.
* **Rolling Updates**: It uses the `community.kubernetes.k8s` module to seamlessly patch existing deployments with the new Docker image tag, ensuring zero-downtime updates.

### 5. **Orchestration with Kubernetes ⛵**
The `kubernetes/` directory contains all the manifests required for deployment.

* **Deployments**: `*-deployment.yaml` files define the desired state for each microservice, including replica counts and container images.
* **Services**: `*-service.yaml` files expose each deployment, enabling inter-service communication.
* **Ingress**: `ingress.yaml` acts as a single entry point, routing external traffic to the correct microservice based on the request path. 
* **Persistence**: `db-pvc.yaml` ensures the database has a **Persistent Volume Claim**, so data is not lost on pod restarts.
* **Secrets**: `hmis-secrets.yaml` securely stores sensitive information like database credentials.

---

## 🚀 Getting Started

Follow these steps to get the HMIS up and running on your local machine.

### **Prerequisites**
* **Operating System**: This setup is designed for a **Linux host** (e.g., Ubuntu).
* **Essential Tools**:
    * **Docker & Docker Compose**: For building and running services locally.
    * **qemu & libvirt**: To create and manage the virtual machine. Install with:
        ```bash
        sudo apt-get install qemu-kvm libvirt-daemon-system libvirt-clients bridge-utils
        ```
    * **Terraform**: To provision the VM.
    * **Ansible**: To configure and deploy to the VM.
    * **SSH**: A public/private key pair to access the VM.

### **Step 1: Set Up the Local Virtual Machine**
1.  **Clone the repository**:
    ```bash
    git clone [https://github.com/your-username/HMIS.git](https://github.com/your-username/HMIS.git)
    cd HMIS/terraform
    ```
2.  **Verify your SSH public key**:
    ```bash
    cat ~/.ssh/id_rsa.pub
    ```
    If this command fails, generate a key pair with `ssh-keygen -t rsa -b 4096`.
3.  **Initialize Terraform**:
    ```bash
    terraform init
    ```
4.  **Create and provision the VM**:
    ```bash
    terraform apply
    ```
    Note the IP address of the VM that Terraform outputs.

### **Step 2: Access the Services**
You have two options to access the services running inside the VM from your host machine.

#### **Option A: Port Forwarding (Recommended)**
1.  Modify your VM's network settings to forward traffic from your host's port 80 to the VM's port 80. This can be done via the Libvirt GUI (`virt-manager`).
2.  Add a line to your host's `/etc/hosts` file to resolve the domain:
    ```bash
    sudo vim /etc/hosts
    # Add this line
    127.0.0.1 hmis.local
    ```
    You can now access the application at `http://hmis.local`.

#### **Option B: Reverse SSH Tunnel**
1.  Establish a reverse SSH tunnel to forward requests from your host's port 8080 to the VM's port 80:
    ```bash
    ssh -N -L 8080:localhost:80 ubuntu@<VM_IP_ADDRESS>
    ```
2.  Access the application via `http://localhost:8080`.

### **Step 3: Deploy to the Kubernetes Cluster**
1.  Ensure your VM is running and you have the IP address.
2.  Run the Ansible playbook from your host machine to install k3s and deploy the application:
    ```bash
    ansible-playbook ansible/playbook.yml -i <VM_IP_ADDRESS>,
    ```

---

## 🤝 Contribution

This project is a community effort! We welcome and encourage contributions. Feel free to fork the repository, create a branch with your enhancements, and submit a pull request.


---


## Credentials
Login credentials for admin are:

Admin Module Email: admin@admin.com

Admin Module Password: admin

## **Credits**: The core framework and initial UI was designed by [MartMbithi](https://github.com/MartMbithi).

