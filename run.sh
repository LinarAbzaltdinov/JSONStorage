#!/bin/bash
if ! which docker; then
  curl -sSL https://get.docker.com/ | sh
  sudo usermod -aG docker $USER
  newgrp docker
fi
if ! which docker-compose; then
  sudo curl -L https://github.com/docker/compose/releases/download/1.19.0/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
  sudo chmod +x /usr/local/bin/docker-compose
fi

docker run -v $(pwd)/app:/app composer install
docker-compose up -d
docker exec phpfpm chmod 777 -R /app/var
docker exec -it postgres psql -U postgres -c "CREATE EXTENSION pg_cron;"
