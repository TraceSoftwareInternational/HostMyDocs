# HostMyDocs

[![](https://images.microbadger.com/badges/image/tracesoftware/hostmydocs.svg)](https://microbadger.com/images/tracesoftware/hostmydocs "Get your own image badge on microbadger.com")
[![Docker Pull](https://img.shields.io/docker/pulls/tracesoftware/hostmydocs.svg)](https://hub.docker.com/r/tracesoftware/hostmydocs/)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)
[![Build Status](https://travis-ci.org/TraceSoftwareInternational/HostMyDocs.svg?branch=master)](https://travis-ci.org/TraceSoftwareInternational/HostMyDocs)

Small web application to quickly access all your documentation !

![Screenshot of HostMyDocs](http://i.imgur.com/FBu4RL4.png)

## Getting Started

Note that by default the BackEnd will require to be secured with HTTPS. If you want to be able to add documentation via unsecured HTTP you will need to pass this environment variable to your container : `SHOULD_SECURE=FALSE`

1) Launch the application and it's server with ```docker run -e CREDENTIALS=user:password -v `pwd`:/data -p 8080:80 tracesoftware/hostmydocs```

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


## Deploy in prod

You can deploy this tools in production with the script `System/install.sh`.
