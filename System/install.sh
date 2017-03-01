#!/usr/bin/env sh

docker pull tracesoftware/hostmydocs

containerId=$(docker container list -q -f name=hostmysdocs-service)
if [[ ! -z $containerId ]]
then
    docker container stop $containerId
    docker container rm $containerId
fi

docker run -d -p 80:80 --env-file=.env --name hostmysdocs-service tracesoftware/hostmydocs

cp -f ./hostmysdocs.service /etc/systemd/system/
systemctl enable hostmysdocs.service
