# Requirements
The penetration test team finished testing a new application. 
It uses multiple micro services and exposes a web interface. The web server is apache2.
The penetration test team identified multiple vulnerabilities in an application.
* Use of "sprintf" in the backend application
* Missing Secure Cookie flag
* .git folder present in the production release
* Userid not invalidated when logging out

The business owner would like to detect these issues earlier in the development process.

In addition, they want to confirm that all the projects are compliant with the following company security requirements:
* Clear text protocols are prohibited
* Private keys should not be world readable
* Weak TLS cipher used (for instance: SSL_RSA_WITH_RC4_128_MD5, SSL_RSA_WITH_RC4_128_SHA, TLS_ECDH_ECDSA_WITH_RC4_128_SHA, TLS_ECDH_RSA_WITH_RC4_128_SHA, TLS_ECDHE_ECDSA_WITH_RC4_128_SHA, TLS_ECDHE_RSA_WITH_RC4_128_SHA)

They ask you to create a tool detecting future occurrences of these vulnerabilities and incompliances.

For the challenge, you will receive:
* A docker-compose.yml
* A frontend container
    *  Dockerfile
    * Apache2/
* Apache config files
    * Source/
* Python app code
* A backend container
    * Dockerfile
    * Source/
* C source files          

You can use any existing tool, language and existing library.

In addition to the tool, we expect you to make recommendations on how to integrate your solution in a CI/CD environment. You can choose the one you are the most familiar with.

You are given 1 week for the challenge. We expect to get a report on the 22 July the latest.
We understand that such a project would require more than a week. We count on you to focus on the most relevant areas and deliver the most value with this budget.
You should write code for some parts while other parts can be just described.
