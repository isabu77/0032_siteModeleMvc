#!/bin/bash

if [ -e .env ]; then
    source .env
else
    echo "Please set up your .env file before starting your environment."
    exit 1
fi


docker-compose build

docker-compose -f docker-compose.yml up -d

sleep 4;

docker exec $CONTAINER_NAME composer update

sleep 4;

# droits d'accès pour télécharger des images :
docker exec $CONTAINER_NAME mkdir public/assets/upload
docker exec $CONTAINER_NAME chmod -R 777 public/assets/upload

if [ $ENV_DEV == true ]; then
    docker exec $CONTAINER_NAME commande/createsql --demo
else
    docker exec $CONTAINER_NAME commande/createsql
fi

echo
echo "#-----------------------------------------------------------"
echo "#"
echo "# Please check your browser to see if it is running, use your"
echo "#"
echo "#-----------------------------------------------------------"
echo

exit 0
