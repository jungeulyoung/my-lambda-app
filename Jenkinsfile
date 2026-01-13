pipeline {
    agent any
    
    environment {
        // AWS 설정 및 리포지토리 정보
        AWS_DEFAULT_REGION = 'ap-northeast-2'
        AWS_ACCOUNT_ID = '123456789012' // 본인 계정 ID로 변경
        ECR_REPO_NAME = 'my-laravel-app'
        LAMBDA_FUNC_NAME = 'my-laravel-function'
        IMAGE_TAG = "${env.BUILD_NUMBER}" // 빌드 번호를 태그로 사용
        ECR_URI = "${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com/${ECR_REPO_NAME}"
    }

    stages {
        stage('1. Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/jungeulyoung90/laravel-jenkins'
            }
        }

        stage('2. Login to ECR') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'aws-credentials-id', usernameVariable: 'AWS_ACCESS_KEY_ID', passwordVariable: 'AWS_SECRET_ACCESS_KEY')]) {
                    sh """
                    # AWS ECR에 도커 로그인을 합니다.
                    aws ecr get-login-password --region $AWS_DEFAULT_REGION | docker login --username AWS --password-stdin ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_DEFAULT_REGION}.amazonaws.com
                    """
                }
            }
        }

        stage('3. Build Docker Image') {
            steps {
                sh """
                # 도커 이미지를 빌드합니다.
                docker build -t ${ECR_URI}:${IMAGE_TAG} .
                docker tag ${ECR_URI}:${IMAGE_TAG} ${ECR_URI}:latest
                """
            }
        }

        stage('4. Push to ECR') {
            steps {
                sh """
                # 빌드된 이미지를 AWS ECR로 업로드합니다.
                docker push ${ECR_URI}:${IMAGE_TAG}
                docker push ${ECR_URI}:latest
                """
            }
        }

        stage('5. Deploy to Lambda') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'aws-credentials-id', usernameVariable: 'AWS_ACCESS_KEY_ID', passwordVariable: 'AWS_SECRET_ACCESS_KEY')]) {
                    sh """
                    # 람다 함수가 방금 올린 새 이미지를 바라보도록 업데이트합니다.
                    aws lambda update-function-code \
                        --function-name $LAMBDA_FUNC_NAME \
                        --image-uri ${ECR_URI}:${IMAGE_TAG} \
                        --region $AWS_DEFAULT_REGION
                    
                    # (선택) 업데이트가 완료될 때까지 기다립니다.
                    aws lambda wait function-updated --function-name $LAMBDA_FUNC_NAME
                    """
                }
            }
        }
    }
}