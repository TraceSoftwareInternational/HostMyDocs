#!/usr/bin/env sh

docker pull tracesoftware/hostmydocs

containerId=$(docker container list -a -q -f name=hostmydocs-service)
if [ ! -z "$containerId" ]
then
    docker container stop $containerId
    docker container rm $containerId
fi

docker run -d -p 80:80 --env-file=.env --name hostmydocs-service tracesoftware/hostmydocs
docker stop hostmydocs-service

cp -f ./hostmydocs.service /etc/systemd/system/
systemctl enable hostmydocs.service
systemctl start hostmydocs.service
