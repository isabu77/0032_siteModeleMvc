#!/bin/bash

docker-compose stop
sleep 3;
docker-compose rm -f
echo "#-----------------------------------------------------------"
echo "#"
echo "#  suppression ok"
echo "#"
echo "#-----------------------------------------------------------"
echo
exit 0