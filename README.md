### Training task DEV-14

Deploy instructions:  

1. Enter Yii project dir `basic/` and install composer packages from root folder
    ```
   composer install
    ```

2. From root dir enter Docker dir  `docker/` and build Docker images
   ```
   docker-compose build
   ```

3. From root dir enter Docker dir  `docker/` andRun Docker containers
   ```
   docker-compose up
   ```
   
4. Run Yii migrations: enter FPM container `docker-php-fpm-1` and execute command
   ```
   php yii migrate
   ```
5. Update hosts file with `phpfpm.local` pointing to localhost (127.0.0.1)
6. Task features should be available at http://phpfpm.local:8080/listing/order/index