
# 파일명: Dockerfile (확장자 없음)

FROM bref/php-8.2-fpm

# 윈도우 줄바꿈 문자(CRLF) 문제 방지를 위해 필요한 경우도 있지만, 
# 보통은 Git 설정으로 해결됩니다. 여기선 기본 설정 유지.

WORKDIR /var/task

COPY . .

# 윈도우에서 만들면 vendor 폴더가 없을 수 있으므로 설치
RUN composer install --optimize-autoloader --no-dev

# 중요: 윈도우 파일 시스템 권한은 리눅스와 다르지만,
# 도커 빌드 내부에서는 리눅스 권한 체계를 따르므로 chmod 사용 가능
RUN chmod -R 755 storage bootstrap/cache

CMD ["public/index.php"]