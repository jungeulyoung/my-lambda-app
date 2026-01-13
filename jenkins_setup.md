# Jenkins CI/CD Setup Guide

This project is configured to deploy to AWS Lambda using Jenkins. Follow these steps to set up your pipeline.

## Prerequisites

1.  **Jenkins Server**: Ensure you have a Jenkins server running with access to:
    -   `docker` (for building images)
    -   `aws-cli` (for deploying to Lambda)
2.  **Plugins**: Install the following plugins in Jenkins:
    -   [Pipeline](https://plugins.jenkins.io/workflow-aggregator/)
    -   [CloudBees AWS Credentials](https://plugins.jenkins.io/aws-credentials/)
    -   [Docker Pipeline](https://plugins.jenkins.io/docker-workflow/)

## Configuration Steps

### 1. Configure AWS Credentials in Jenkins

1.  Go to **Manage Jenkins** > **Manage Credentials**.
2.  Select a domain (e.g., `Start` or `Global`).
3.  Click **Add Credentials**.
4.  **Kind**: Select `AWS Credentials`.
5.  **Scope**: `Global` (or as appropriate).
6.  **ID**: Enter `aws-credentials-id` (This matches the ID used in the `Jenkinsfile`).
7.  **Access Key ID**: Enter your AWS Access Key.
8.  **Secret Access Key**: Enter your AWS Secret Key.
9.  Click **Create**.

### 2. Configure Environment Variables

Open the `Jenkinsfile` in the project root and update the `environment` section with your specific AWS details:

```groovy
environment {
    AWS_DEFAULT_REGION = 'ap-northeast-2' // Your target Region
    AWS_ACCOUNT_ID = '123456789012'       // Your AWS Account ID
    ECR_REPO_NAME = 'my-laravel-repo'     // Name of your ECR Repository
    LAMBDA_FUNC_NAME = 'my-laravel-function' // Name of your Lambda Function
    // ...
}
```

### 3. Create the Pipeline Job

1.  Go to Jenkins Dashboard > **New Item**.
2.  Enter a name (e.g., `Laravel-Lambda-Deploy`) and select **Pipeline**.
3.  Click **OK**.
4.  Scroll down to the **Pipeline** section.
5.  **Definition**: Select `Pipeline script from SCM`.
6.  **SCM**: Select `Git`.
7.  **Repository URL**: Enter your repository URL (e.g., `https://github.com/jungeulyoung90/laravel-jenkins`).
    -   *Note: Ensure Jenkins has permission to access this repository if it's private.*
8.  **Branch Specifier**: `*/main` (or your default branch).
9.  **Script Path**: `Jenkinsfile`.
10. Click **Save**.

## Pipeline Stages

The pipeline consists of the following stages:

1.  **Checkout**: Pulls the latest code from the repository.
2.  **Login to ECR**: Authenticates with AWS ECR using the configured credentials.
3.  **Build Docker Image**: Builds the Docker image from `Dockerfile` and tags it.
4.  **Push to ECR**: Pushes the built image to your ECR repository.
5.  **Deploy to Lambda**: Updates the AWS Lambda function to use the new container image.

## Troubleshooting

-   **Docker Permission Denied**: If Jenkins cannot run docker commands, ensure the `jenkins` user is added to the `docker` group on the server (`sudo usermod -aG docker jenkins`).
-   **AWS CLI Not Found**: Ensure `aws` CLI is installed on the machine running the Jenkins agent.
