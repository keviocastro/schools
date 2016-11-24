sudo service apache2 stop
sudo service mysql stop
docker-compose up -d
docker exec -it schools_web_1 bash
