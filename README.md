# HostMyDocs

[![](https://images.microbadger.com/badges/image/tracesoftware/hostmydocs.svg)](https://microbadger.com/images/tracesoftware/hostmydocs "Get your own image badge on microbadger.com")
[![Docker Pull](https://img.shields.io/docker/pulls/tracesoftware/hostmydocs.svg)](https://hub.docker.com/r/tracesoftware/hostmydocs/)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)
[![Build Status](https://travis-ci.org/TraceSoftwareInternational/HostMyDocs.svg?branch=master)](https://travis-ci.org/TraceSoftwareInternational/HostMyDocs)

Small web application to quickly access all your documentation !

![Screenshot of HostMyDocs](http://i.imgur.com/uAVV722.png)

## Getting Started

1) Launch the application and it's server with `docker run -p 8080:80 tracesoftware/hostmydocs`.

2) Put all the documentation files and the `index.html` file into a folder

3) Zip that folder !

4) Now upload it with cURL by example :
``` bash
curl --request POST \
--url http://localhost:8080/BackEnd/projects \
--header 'content-type: multipart/form-data; boundary=---011000010111000001101001' \
--form name=DocumentationName \
--form version=0.1.0 \
--form language=YourProgrammingLanguage \
--form archive=@/path/to/your/file.zip
```

5) Open [http://localhost:8080](http://localhost:8080) to see your uploaded docs !

## BackEnd API

You can visualize it [in the Swagger editor](http://editor.swagger.io/#/?import=https://cdn.rawgit.com/TraceSoftwareInternational/HostMyDocs/master/BackEnd/specs/swagger.yaml)
