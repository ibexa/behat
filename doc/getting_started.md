# Getting started

Ibexa/behat repository was created to make development of [Behat](https://behat.org/en/latest/) tests for Ibexa DXP easier and quicker. If you're new to Behat you can get familiar with it by browsing [Behat's documentation](https://docs.behat.org/en/latest/guides.html).

# How can I use Behat in my project?

This repository has higher testing levels (system testing and acceptance testing) in mind. You can use it to:
* put your system under test in the desired state
  - create necessary Content Types, Content items, Users etc.
  - create required YAML configuration using code
  - put required code examples in place
* test your system with a browser
  - make use of our browser test framework to speed up your development
  - in case of test failure get access to browser and server logs and take automatic screenshots

# More reading

Ibexa uses following libraries for Behat Development:
- [Behat](https://behat.org/en/latest/) as a BDD framework for PHP
- [Mink](http://mink.behat.org/en/latest/) as browser driver abstraction
- [Faker](https://github.com/fzaninotto/Faker) for random data generation
- [PHPUnit](https://phpunit.de/) for test assertions
- [Fastest](https://github.com/liuggio/fastest) as a parallel test runner
