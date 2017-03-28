# HostMyDocs

[![](https://images.microbadger.com/badges/image/tracesoftware/hostmydocs.svg)](https://microbadger.com/images/tracesoftware/hostmydocs "Get your own image badge on microbadger.com")
[![Docker Pull](https://img.shields.io/docker/pulls/tracesoftware/hostmydocs.svg)](https://hub.docker.com/r/tracesoftware/hostmydocs/)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)
[![Build Status](https://travis-ci.org/TraceSoftwareInternational/HostMyDocs.svg?branch=master)](https://travis-ci.org/TraceSoftwareInternational/HostMyDocs)

Small web application to quickly access all your documentation !

![Screenshot of HostMyDocs](http://i.imgur.com/9SpGBdT.png)

## Before Starting

Note that by default the BackEnd will require to be secured with HTTPS. If you want to be able to add documentation via unsecured HTTP you will need to pass the `SHOULD_SECURE=FALSE` environment variable to your container.

You can refer to the [production setup section](#production-setup) to properly deploy an HostMyDocs instance with SSL enabled.

## Getting Started

1) Launch the application and its server with ```docker run -e CREDENTIALS=user:password -e SHOULD_SECURE=FALSE -v `pwd`:/data -p 8080:80 tracesoftware/hostmydocs```

2) Put all the documentation files and the `index.html` file into a folder

3) Zip that folder !

4) Now upload it with cURL by example :

``` bash
curl --request POST \
  --url http://localhost:8080/BackEnd/addProject \
  --user user:password \
  --header 'content-type: multipart/form-data;authorization: Basic dXNlcjpwYXNzd29yZA==' \
  --header 'boundary=---011000010111000001101001' \
  -F "name=DocumentationName" \
  -F "version=1.0.0" \
  -F "language=YourProgrammingLanguage" \
  -F "archive=@YourZipFile.zip;type=application/zip"
```

5) Open [http://localhost:8080](http://localhost:8080) to see your uploaded docs !

## BackEnd API

You can visualize it [in the Swagger editor](http://editor.swagger.io/#/?import=https://cdn.rawgit.com/TraceSoftwareInternational/HostMyDocs/master/BackEnd/specs/swagger.yaml)

## Production setup

You only need to put the `System` folder on your server to be able to deploy the application.

First, you must modify the `System/.env` file to change the default credentials that are not secure.
Then, you can execute the `System/install.sh` script. It will register a `hostmydocs.service` in `systemctl` so you can easily access to logs.
If you want to upgrade the application, just run `System/install.sh` again !

### SSL management

The container provide two volumes, so you can mount your certificate and your private key

- `/etc/ssl/certs/ssl-cert-snakeoil.pem`
- `/etc/ssl/private/ssl-cert-snakeoil.key`
