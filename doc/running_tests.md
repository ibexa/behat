# Running tests

## Configuration

In order to use behat you need to use `behat` Symfony environment (which is defined in Ibexa by default). It is also recommended to run in enabled debug mode, which gives you more detailed browser screenshots in case of failure.

The standard behat configuration file is [behat_ibexa_oss.yaml](https://github.com/ibexa/behat/blob/4.6/behat_ibexa_oss.yaml), located in ibexa/behat directory. There you can:
- in the `Behat\MinkExtension` section:
  - set the URL of your website for browser testing (`base_url` parameter)
  - set driver configuration (for example `wd_host` for Selenium Server)
  - see [MinkExtension documentation](https://github.com/Behat/MinkExtension/blob/master/doc/index.rst) for more information
- in the `Bex\Behat\ScreenshotExtension` section:
  - set your Cloudinary account details (`cloud_name` and `preset` parameter) to specify where the screenshots are uploaded
  - see [Cloudinary screenshot driver doc](https://github.com/ezsystems/behat-screenshot-image-driver-cloudinary/blob/master/README.md) for more details about the Cloudinary integration and [elvetemedve/behat-screenshot](https://github.com/elvetemedve/behat-screenshot) for other screenshots configuration options 

Behat profiles and suites are not defined in this file, but imported from files specified at the top.

### Running browser tests

If you want to run browser tests you need to have Selenium Server runnning. One way of doing this is running our Docker stack, which has built-in support for it: see [Ibexa Docker blueprints](https://github.com/ibexa/docker/blob/4.6/README.md#behat-and-selenium-use) documentation for more information.

Another way is to use the Selenium Server Docker container and setting it up manually. Look at [manifest.json file](https://github.com/ibexa/recipes-dev/blob/master/ibexa/docker/4.6/manifest.json#L23) for the currently used version.

It can be set up using examples from [docker-selenium](https://github.com/SeleniumHQ/docker-selenium)
Example for Chrome would be: 
`docker run -d -p 4444:4444 -p 7900:7900 --shm-size="2g" selenium/standalone-chrome:latest`

Where: 
- 4444 is the port where Selenium Server will be accessible 
- 5900 is the port where the VNC client is accessible (to preview running tests) 
- shm-size is related to Chrome containers requiring more memory (see [Selenium container configuration](https://github.com/ibexa/docker/blob/4.6/docker/selenium.yml#L19))

After the container is set up correctly you need to adjust the configuration of `selenium2` driver in `behat_ibexa_oss.yaml` file

## Running tests

### Running standard Behat tests

Behat comes with a wrapper for the standard Behat runner: [ibexabehat](https://github.com/ibexa/behat/blob/4.6/bin/ibexabehat) to make running tests in parallel easier.

Use:
```
# standard Behat runner
bin/ibexabehat --mode=standard --profile=profileName --tags=exampleTag
bin/ibexabehat -m=standard -p=profileName -s=suiteName -t=exampleTag
```
```
# parallel Behat runner
bin/ibexabehat -m=parallel -p=profileName -s=suiteName
bin/ibexabehat --profile=profileName --suite=suiteName
```

Running Behat feature files in parallel (on the available number of CPUs) is the default option when mode is not specified. See the script documentation for more examples.

## Existing test profiles and suites

By convention profiles and suites are defined in the `behat_suites.yml` file in each bundle, if they exist. See [Behat suites](../behat_suites.yml) and [AdminUI suites](https://github.com/ibexa/admin-ui/blob/4.6/behat_suites.yml) for examples.

In order to run them, execute:
- `bin/ibexabehat --profile=behat --suite=examples` (behat usage examples)
- `bin/ibexabehat --profile=adminui --suite=adminui` (all AdminUI tests)

## Previewing browser tests

The Selenium Server container comes with VNC server that allows you to preview browser tests when they're running. It runs on port 5900 and is protected by password `secret`. 

See [Docker Selenium documentation on debugging](https://github.com/SeleniumHQ/docker-selenium#debugging) for more details.
