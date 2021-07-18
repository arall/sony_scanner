# Sony interview challenge

## Challenge requirements

Check [CHALLENGE.md](./CHALLENGE.md).

## Solution

### Motivation

I believe the tool should be automated, agnostic and unlimited. Meaning it aims for the less user interaction as possible, compatible with different types of target technologies and configurations, and with the less target scope restriction as possible.
For example, it should be able to automatically identify open ports, and run the necessary checks on those, instead of the users defining the list of ports to check in the target scope, as developers might leave open ports accidentally.

Based on that approach, the tool will start from a [YAML config file](#commands), where the initial target scope is defined.
From that starting point, first it will run a **Discovery** phase, obtaining the list of open ports from the initial targets, and the websites that run in those ports (if any).
Once the Discovery phase is finished, it will run the **Audit** phase on each asset that was found, and of course to the ones defined initially. This phase includes checking for the Secure cookie flag, the use of SSL/TLS weak ciphers, use of HTTPS instead of HTTPS, and others (as defined in the Challenge requirements).

For executing the tasks both phases, the tool use [**Modules**](#modules), which are small scripts that run against a target.

In order to collect, store and relate different entities (called [**Models**](#models)), the tool uses a small [database](#database).
This can used further to keep the track of open / fixed findings, passive audits based on software versions and public vulnerabilities databases, alerting... Without the need of re-creating the whole database structure on each run.

### Technology

I've decided to write the tool in PHP, as is the language I'm more familiar with, using [Laravel Zero](https://laravel-zero.com/) framework, a lightweight and modular micro-framework for developing console applications.

The tool uses the following 3rd party tools:

- [nmap](https://nmap.org/), for discovering open ports on targets.
- [testssl.sh](https://github.com/drwetter/testssl.sh), for auditing SSL/TLS certificates, including weak ciphers.
- [semgrep](https://semgrep.dev/), for performing statical analysis in the target source code, such as looking for `sprintf` method calls in C code.
- [docker](https://www.docker.com/), for providing a ready-to-use image, but also for executing commands in the target containers, such as checking file permissions in private keys.

### Architecture

#### Models

The list of Models are:

Target-related assets:
- **Certificate**: Represents a Certificate used in Websites (for the sack of simplicity).
- **Docker**: Represents a Docker container.
- **Host**: Represents a Host.
- **Port**: Represents a Port opened in a Host.
- **Repository**: Represents a directory with code.
- **Website**: Represents a Website URL.

Audit-related models:
- **Severity**: Indicates the grade of importance of an issue, as Informational, Low, Medium, High and Critical.
- **Vulnerability**: Describes a vulnerability type, such as Missing Secure Cookie Flags, or Use of non-encrypted protocols (HTTP)
- **Finding**: Represents a potential security issue identified by the tool, relating an asset with a vulnerability and a severity.

##### Modules

As described perviously, there are two types of Modules, the Discovery and the Audit ones. 
Discovery will, from one target Model, create other potential related target Models. For example, from a Host, `nmap` module will create Ports Models, based on the discovered open ports.
And Audit will only produce Finding Models in return. For example the `HTTP` module will run against a Website, and will produce a Finding with `HTTP` vulnerability if the website is server over HTTP.

The Modules are built in a way that are easy to extend or implement new ones. First the `init` method is called (to setup pre-run requirements), then the `canRun` method checks if the target is suitable to the module, then the `run` method will execute the main steps, and lastly the `finish` method will perform some cleanup if needed.

A further nice to have feature would be to, once a Module is executed again, mark the related Findings as fixed automatically (for example a website is no longer available in HTTP), or remove / update the related Models if those changed (for example a port is no longer opened).

The list of Modules are:

###### Discovery

|Code              |From    |Generates   |Description                                      |
|------------------|--------|------------|-------------------------------------------------|
|Hosts\Ports\Nmap  |Hosts   |Ports       |Enumerates open Ports of a Host using nmap.      |
|Ports\Websites    |Ports   |Websites    |Checks if a wesite is served on a specific Port. |

###### Audit

|Code                           |From         |Description                                                                    |
|-------------------------------|-------------|-------------------------------------------------------------------------------|
|Docker\PrivateKeyWorldReadable |Dockers      |Looks for private key files with world readable permissions.                   |
|Repositories\Semgrep           |Repositories |Uses https://semgrep.dev/ to perform static security analysis on repositories. |
|Websites\Cookies               |Websites     |Checks if the website cookies are using the security flags.                    |
|Websites\ExposedVCS            |Websites     |Checks if the website is exposing GIT or SVN directories over HTTP.            |
|Websites\HTTP                  |Websites     |Checks if the website is served over HTTP.                                     |
|Websites\Logout                |Websites     |Checks if the cookies are invalidated after logout.                           |
|Website\TestSSL                |Websites     |Audits SSL/TLS Vulnerabilities by using TestSSL.sh.                            |

## Setup

### ENV

Make a copy of the `.env.example` file and rename it as `.env`. You can define the following settings there.

#### Database

By default, a local SQLite database located at `databases/database.sqlite` will be used. 

If you want to use an external MySQL database (for example), you can use the following settings:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=interviews_sony
DB_USERNAME=root
DB_PASSWORD=root
```

Please check `config/database.php` for more details.

#### Tools

The paths to the security tools binaries are configured with the following variables:

```
TOOLS_TESTSSL=/opt/testssl.sh/testssl.sh
TOOLS_NMAP=nmap
TOOLS_SEMGREP=semgrep
```

Those are the default locations for a Docker setup. If you're running the script directly in your host, adjust those if necessary.

### Docker

Build the image with `docker-compose build`.

Run the scanner with:
`docker-compose run -v ${PWD}:/tmp/ scanner config /tmp/config.yml --clear-db`

Or run a bash session with:
`docker-compose run -v ${PWD}:/tmp/ --entrypoint /bin/bash scanner`

Notes: Since the tool requires to communicate with other docker containers, there are some additional configurations that might need to be adjusted in the `docker-compose.yml` file.
* The Docker socket must be provided as a volume in docker-compose.yml (`/var/run/docker.sock:/var/run/docker.sock`). This path might be different from `Win/macOS/Linux` environments.
* The Docker network of the target containers must be specified. By default (as an example), uses `challenge_default`.
* When building and pushing the image under non amd64 hosts (for example, Apple M1), the CI/CD pipeline might fail (at least in the example setup in Github). In that case, use `platform: linux/amd64` to force to use that architecture.

### Host

##### Requirements

Dependencies:
- PHP >= 7.4
- [Composer](https://getcomposer.org/)

Security tools:
- [nmap](https://nmap.org/)
- [testssl.sh](https://github.com/drwetter/testssl.sh)
- [semgrep](https://semgrep.dev/)
- [docker](https://www.docker.com/)

Install the composer dependencies:
`composer install`

### CI/CD

The tool will return exit code 1 if there are any Findings, or exit code 0 if there aren't.

I've implemented a [functional demo pipeline](https://github.com/arall/sony_challenge/blob/master/.github/workflows/scan.yml) using [Github Actions](https://github.com/arall/sony_challenge/actions), targeting the challenge project.

##### Setup

Run the database migrations and seeders:
`php scanner migrate --seed`

## Usage

Example: `./scanner config <config.yml> --clear-db`

### Commands

There is only one command available, due the requirements defined:

* `config`: Execute a scan based on a config YAML file.

The `config.yml` file uses the following structure (example for the challenge requirements):

```
# List of hosts to scan (Ips, hostnames or docker service names can be used)
hosts:
  - web
# List of websites to scan
websites:
  # Port is optional, https prefix will use 80 and https will use 443. If no prefix is provided, http will be used.
  # If non-standard ports are used, make sure you set the container port, not the one mapped externally in the docker host.
  - url: https://web:443
    # Auth is optional. 
    # If defined, will be used in Audit\Websites\Logout and Audit\Websites\Cookies modules.
    auth:
      # URI to use in the login request
      uri: /login
      # Method to use in the login request
      method: POST
      # Form data to send in the login request
      data:
        user: admin
        pass: admin123
      # GET url to check if the user is authenticated (must return response code 200)
      check: /dashboard
      # GET url to logout
      logout: /logout
# List of paths to use as repositories
repositories:
  - /tmp/challenge/
# List of docker containers
dockers:
  - challenge_web_1
```

### Options

* `--clear-db`: Will clear the assets from the database on each run.

## Further Improvements

* Validation of config.yml files
* Multi threading
* Allow different website auth methods (basic auth, barer token, json body...)
* Allow different methods for check and logout
* Testing
* More modules, of course :)
